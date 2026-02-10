<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class due_date_reminder extends Model
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
     * Get the expiration date
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function expirationDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $time = new DateTime(date($value . '-m-Y'));
                return $time->format(config('app.date_format'));
            },
        );
    }

    /**
     * Get the notification date
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function notificationDate(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $time = new DateTime(date($value . '-m-Y'));
                return $time->format(config('app.date_format'));
            },
        );
    }
}
