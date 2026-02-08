<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pppoe_profile extends Model
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
     * Get the IPv4Pool that owns the pppoe profile.
     */
    public function ipv4pool()
    {
        return $this->belongsTo(ipv4pool::class)->withDefault();
    }

    /**
     * Get the IPv6Pool that owns the pppoe profile.
     */
    public function ipv6pool()
    {
        return $this->belongsTo(ipv6pool::class)->withDefault();
    }

    /**
     * Get the packages for the pppoe profile
     */
    public function master_packages()
    {
        return $this->hasMany(master_package::class);
    }
}
