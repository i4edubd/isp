<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer_bill extends Model
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
    protected $appends = ['suspension_date'];

    /**
     * Interact with the amount.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function amount(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => round($value),
        );
    }

    /**
     * Get the customer zone that owns the bill.
     */
    public function customer_zone()
    {
        return $this->belongsTo(customer_zone::class, 'customer_zone_id', 'id')->withDefault();
    }

    /**
     * Get the package that owns the bill.
     */
    public function package()
    {
        return $this->belongsTo(package::class)->withDefault();
    }

    /**
     * Get the suspension date
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function suspensionDate(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $due_date = date_format(date_create($attributes['due_date']), config('app.date_format'));
                return Carbon::createFromFormat(config('app.date_format'), $due_date, getTimeZone($attributes['operator_id']))
                    ->addDay()
                    ->format(config('app.date_format'));
            },
        );
    }
}
