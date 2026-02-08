<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cash_out extends Model
{
    use HasFactory;

    /**
     * The model type
     *
     * @var string|null (node|central)
     */
    protected $modelType = 'central';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['description', 'transaction_type', 'transaction'];

    /**
     * Set connection for Central Model if (host_type === 'node')
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        if (config('local.host_type', 'central') === 'node') {
            if ($this->modelType === 'central') {
                $this->connection = config('database.central', 'mysql');
            }
        }

        parent::__construct($attributes);
    }

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the description.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function description(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                switch ($attributes['transaction_code']) {
                    case '1':
                        return "Customer Payment";
                        break;
                    case '2':
                        return "Subscription Payment";
                        break;
                    case '3':
                        return "Cash Out";
                        break;
                    case '4':
                        return "Admin Credit";
                        break;
                    case '5':
                        return "Online Recharge";
                        break;
                    case '6':
                        return "Exchange";
                        break;
                    case '7':
                        return "Online Account Payment";
                        break;
                    default:
                        return "Unknown";
                        break;
                }
            },
        );
    }

    /**
     * Get the description.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function transactionType(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => "Cash Out",
        );
    }

    /**
     * Get the transaction that owns the cash out.
     * 
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function transaction(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($attributes['transaction_code'] == 1) {
                    return customer_payment::where('id', $attributes['transaction_id'])->first();
                }
                if ($attributes['transaction_code'] == 7) {
                    return operators_online_payment::where('id', $attributes['transaction_id'])->first();
                }
                return false;
            },
        );
    }
}
