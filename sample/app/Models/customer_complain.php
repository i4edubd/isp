<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer_complain extends Model
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
    protected $appends = ['status_color', 'elapsed_time'];

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
     * Get the Operator that owns the complaint.
     */
    public function ackBy()
    {
        return $this->belongsTo(operator::class, 'ack_by', 'id')->withDefault();
    }

    /**
     * Get the Categor that owns the complaint.
     */
    public function category()
    {
        return $this->belongsTo(complain_category::class, 'category_id', 'id')->withDefault();
    }

    /**
     * Get the Department that owns the complaint.
     */
    public function department()
    {
        return $this->belongsTo(department::class, 'department_id', 'id')->withDefault();
    }

    /**
     * Get color attribute
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function elapsedTime(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (strlen($attributes['diff_in_seconds'])) {
                    return $attributes['diff_in_seconds'];
                }
                return Carbon::createFromFormat(config('app.date_time_format'), $attributes['start_time'], config('app.timezone'))->diffInSeconds(Carbon::now(config('app.timezone')));
            },
        );
    }

    /**
     * Get color attribute
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function statusColor(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                $color = "text-dark";

                if ($attributes['status'] == 'In Progress') {
                    $color = "text-success";
                }

                if ($attributes['status'] == 'On Hold') {
                    $color = "text-warning";
                }

                return $color;
            },
        );
    }

    /**
     * Get the receiver that owns the complaint.
     */
    public function receiver()
    {
        return $this->belongsTo(operator::class, 'receiver_id', 'id')->withDefault();
    }
}
