<?php

namespace App\Models;

use App\Models\Freeradius\customer;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class master_package extends Model
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
    protected $appends = ['customer_count', 'readable_rate_unit', 'total_minute', 'total_octet_limit', 'validity_in_days', 'validity_in_hours', 'validity_in_minutes'];

    /**
     * Calculate the total customer count.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function customerCount(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $key = 'master_package_customerCount_' . $attributes['id'];
                $ttl = 300;
                return Cache::remember($key, $ttl, function () use ($attributes) {
                    $master = operator::find($attributes['mgid']);
                    $customer_count = 0;
                    $packages = package::where('mpid', $attributes['id'])->get();
                    foreach ($packages as $package) {
                        $model = new customer();
                        $model->setConnection($master->node_connection);
                        $customers = $model->where('package_id', $package->id)->count();
                        $customer_count = $customer_count + $customers;
                    }
                    return $customer_count;
                });
            },
        );
    }

    /**
     * Get the fair usage policy associated with the package.
     */
    public function fair_usage_policy()
    {
        return $this->hasOne(fair_usage_policy::class, 'master_package_id', 'id');
    }

    /**
     * Get the operator's associated with the package
     */
    public function operators()
    {
        return $this->belongsToMany(operator::class, 'packages', 'mpid', 'operator_id', 'id', 'id');
    }

    /**
     * Get the packages that's belongs to this master package
     */
    public function packages()
    {
        return $this->hasMany(package::class, 'mpid', 'id');
    }

    /**
     * Get the PPPoE profile that owns the package
     */
    public function  pppoe_profile()
    {
        return $this->belongsTo(pppoe_profile::class)->withDefault();
    }

    /**
     * Get Readable Rate Unit
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function readableRateUnit(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($attributes['rate_unit'] === 'M') {
                    return 'Mbps';
                } else {
                    return 'Kbps';
                }
            }
        );
    }

    /**
     * Calculate the total minute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function totalMinute(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                if ($attributes['validity_unit'] === 'Day') {
                    return $attributes['validity'] * 1440;
                }

                if ($attributes['validity_unit'] === 'Hour') {
                    return $attributes['validity'] * 60;
                }

                return $attributes['validity'];
            }
        );
    }

    /**
     * Calculate the total octet limit.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function totalOctetLimit(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($attributes['volume_unit'] === 'GB') {
                    return $attributes['volume_limit'] * 1000 * 1000 * 1000;
                } else {
                    return $attributes['volume_limit'] * 1000 * 1000;
                }
            },
        );
    }

    /**
     * Calculate the validity in days
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function validityInDays(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                if ($attributes['validity_unit'] === 'Day') {
                    return $attributes['validity'];
                }

                if ($attributes['validity_unit'] === 'Hour') {
                    return $attributes['validity'] / 24;
                }

                return $attributes['validity'] / 1440;
            }
        );
    }

    /**
     * Calculate the validity in Hours
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function validityInHours(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                if ($attributes['validity_unit'] === 'Day') {
                    return $attributes['validity'] * 24;
                }

                if ($attributes['validity_unit'] === 'Hour') {
                    return $attributes['validity'];
                }

                return $attributes['validity'] / 60;
            }
        );
    }

    /**
     * Calculate the validity in Minutes
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function validityInMinutes(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                if ($attributes['validity_unit'] === 'Day') {
                    return $attributes['validity'] * 1440;
                }

                if ($attributes['validity_unit'] === 'Hour') {
                    return $attributes['validity'] * 60;
                }

                return $attributes['validity'];
            }
        );
    }
}
