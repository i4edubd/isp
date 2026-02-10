<?php

namespace App\Models;

use App\Models\Freeradius\customer;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class package extends Model
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
     * Get the operator that owns the package.
     */
    public function operator()
    {
        return $this->belongsTo(operator::class, 'operator_id', 'id');
    }

    /**
     * Get the master package that owns the package.
     */
    public function master_package()
    {
        return $this->belongsTo(master_package::class, 'mpid', 'id');
    }

    /**
     * Get the master package that owns the package.
     */
    public function parent_package()
    {
        return $this->belongsTo(package::class, 'ppid', 'id');
    }

    /**
     * Get the master package that owns the package.
     */
    public function child_packages()
    {
        return $this->hasMany(package::class, 'ppid', 'id');
    }

    /**
     * Get the price.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function price(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value > 0 ? $value : 1,
        );
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['customer_count'];

    /**
     * Calculate the total customer.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function customerCount(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $key = 'package_customerCount_' . $attributes['id'];
                $ttl = 300;
                return Cache::remember($key, $ttl, function () use ($attributes) {
                    $master = operator::find($attributes['mgid']);
                    $customer_count = 0;
                    $model = new customer();
                    $model->setConnection($master->node_connection);
                    $customers = $model->where('package_id', $attributes['id'])->count();
                    $customer_count = $customer_count + $customers;
                    return $customer_count;
                });
            },
        );
    }
}
