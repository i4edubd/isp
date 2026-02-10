<?php

namespace App\Models;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class billing_profile extends Model
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['name', 'grace_period', 'payment_date', 'next_payment_date', 'end_of_billing_cycle', 'period_format', 'due_date_figure'];

    /**
     * Get the minimum validity.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function minimumValidity(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value > 0 ? $value : 1,
        );
    }

    /**
     * Get the Billing profile name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                if (is_null($attributes['profile_name']) == false) {
                    return $attributes['profile_name'];
                }

                $max = date('t');
                $billing_due_date = $attributes['billing_due_date'] > $max ? $max : $attributes['billing_due_date'];
                $time = new DateTime(date($billing_due_date . '-m-Y'));
                $payment_date = $time->format(config('app.date_format'));
                return 'Payment Date : ' . $payment_date;
            },
        );
    }

    /**
     * Get the grace_period.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function gracePeriod(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                if ($attributes['billing_type'] !== 'Monthly') {
                    return 0;
                }

                if ($attributes['cycle_ends_with_month'] == 'yes') {
                    return $attributes['billing_due_date'];
                } else {
                    return 0;
                }
            },
        );
    }

    /**
     * Get the payment date.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function paymentDate(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $max = date('t');
                $billing_due_date = $attributes['billing_due_date'] > $max ? $max : $attributes['billing_due_date'];
                $time = new DateTime(date($billing_due_date . '-m-Y'));
                return $time->format(config('app.date_format'));
            },
        );
    }

    /**
     * Get the next payment date.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function nextPaymentDate(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $max = date('t');
                $billing_due_date = $attributes['billing_due_date'] > $max ? $max : $attributes['billing_due_date'];
                $time = new DateTime(date($billing_due_date . '-m-Y'));
                $payment_date = $time->format(config('app.date_format'));
                return Carbon::createFromFormat(config('app.date_format'), $payment_date, getTimeZone($attributes['mgid']))->addMonth()->format(config('app.date_format'));
            },
        );
    }

    /**
     * Get the next payment date.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function endOfBillingCycle(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($attributes['cycle_ends_with_month'] == 'yes') {
                    return date('t-m-Y');
                } else {
                    // Next Payment Date
                    $max = date('t');
                    $billing_due_date = $attributes['billing_due_date'] > $max ? $max : $attributes['billing_due_date'];
                    $time = new DateTime(date($billing_due_date . '-m-Y'));
                    $payment_date = $time->format(config('app.date_format'));
                    return Carbon::createFromFormat(config('app.date_format'), $payment_date, getTimeZone($attributes['mgid']))->addMonth()->format(config('app.date_format'));
                }
            },
        );
    }

    /**
     * Get period_format.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function periodFormat(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($attributes['cycle_ends_with_month'] == 'yes') {
                    return 'F-Y';
                } else {
                    return 'd M Y';
                }
            },
        );
    }

    /**
     * Get profile name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function profileName(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (strlen($value)) {
                    return $value;
                }
                $max = date('t');
                $billing_due_date = $attributes['billing_due_date'] > $max ? $max : $attributes['billing_due_date'];
                $time = new DateTime(date($billing_due_date . '-m-Y'));
                $payment_date = $time->format(config('app.date_format'));
                return 'Payment Date : ' . $payment_date;
            },
        );
    }

    /**
     * GET Due Date Figure.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function dueDateFigure(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                if (strlen($attributes['profile_name'])) {
                    return $attributes['profile_name'];
                }

                switch ($attributes['billing_type']) {
                    case 'Free':
                        return $attributes['profile_name'];
                        break;
                    case 'Daily':
                        return $attributes['profile_name'];
                        break;
                }

                switch ($attributes['billing_due_date']) {
                    case '1':
                    case '21':
                    case '31':
                        return $attributes['billing_due_date'] . 'st day of each month';
                        break;

                    case '2':
                    case '22':
                        return $attributes['billing_due_date'] . 'nd day of each month';
                        break;

                    case '3':
                    case '23':
                        return $attributes['billing_due_date'] . 'rd day of each month';
                        break;

                    default:
                        return $attributes['billing_due_date'] . 'th day of each month';
                        break;
                }
            },
        );
    }
}
