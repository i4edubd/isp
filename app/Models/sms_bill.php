<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sms_bill extends Model
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
     * Get the operator that owns the SMS Bill.
     */
    public function operator()
    {
        return $this->belongsTo(operator::class);
    }


    /**
     * Get the Payment Gateways for the sms bill.
     */
    public function payment_gateways()
    {
        return $this->hasMany(payment_gateway::class, 'operator_id', 'merchant_id');
    }
}
