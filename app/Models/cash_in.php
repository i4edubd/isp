<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cash_in extends Model
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
                        return "Affiliate Commission";
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
            get: fn ($value) => "Cash In",
        );
    }

    /**
     * Get the transaction.
     *
     */
    public function transaction()
    {
        if ($this->attributes['transaction_code'] == 2) {
            return $this->belongsTo(subscription_payment::class, 'transaction_id', 'id');
        }
        if ($this->attributes['transaction_code'] == 5) {
            return $this->belongsTo(operators_online_payment::class, 'transaction_id', 'id');
        }
        return $this->belongsTo(customer_payment::class, 'transaction_id', 'id')->withDefault();
    }
}
