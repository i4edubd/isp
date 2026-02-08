<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class fair_usage_policy extends Model
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
    protected $appends = ['total_octet_limit'];

    /**
     * Get the package that owns the policy.
     */
    public function master_package()
    {
        return $this->belongsTo(master_package::class, 'master_package_id', 'id')->withDefault();
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
                return $attributes['data_limit'] * 1000 * 1000 * 1000;
            },
        );
    }

    /**
     * Get the ipv4 pool that owns the policy.
     */
    public function ipv4pool()
    {
        return $this->belongsTo(ipv4pool::class, 'ipv4pool_id', 'id')->withDefault();
    }
}
