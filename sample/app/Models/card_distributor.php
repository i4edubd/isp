<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class card_distributor extends Authenticatable
{
    use HasFactory;

    /**
     * The model type
     *
     * @var string|null (node|central)
     */
    protected $modelType = 'central';

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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['account_balance'];

    /**
     * Get Account balance.
     * Will be used for Debit/Prepaid reseller's balance check
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function accountBalance(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                return (0 - $attributes['amount_due']);
            }
        );
    }

    /**
     * Get payments
     */
    public function card_distributor_payments()
    {
        return $this->hasMany(card_distributor_payments::class, 'card_distributor_id', 'id');
    }
}
