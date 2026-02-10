<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class operator_payment extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the operator that owns the payment.
     */
    public function operator()
    {
        return $this->belongsTo(operator::class, 'operator_id', 'id')->withDefault();
    }

    /**
     * Get the cash_collector that owns the payment.
     */
    public function cash_collector()
    {
        return $this->belongsTo(operator::class, 'cash_collector_id', 'id')->withDefault();
    }

    /**
     * Get the payment gateway that owns the payment.
     */
    public function payment_gateway()
    {
        return $this->belongsTo(payment_gateway::class, 'payment_gateway_id', 'id')->withDefault();
    }

    /**
     * Interact with the amount.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function amountPaid(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => round($value),
        );
    }
}
