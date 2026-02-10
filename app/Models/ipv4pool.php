<?php

namespace App\Models;

use App\Models\Freeradius\customer;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ipv4pool extends Model
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
    protected $appends = ['used_space'];

    /**
     * Get the address for the pool.
     */
    public function ipv4address()
    {
        return $this->hasMany(ipv4address::class);
    }

    /**
     * Get the PPPoE Profiles for the pool.
     */
    public function pppoe_profiles()
    {
        return $this->hasMany(pppoe_profile::class, 'ipv4pool_id', 'id');
    }

    /**
     * Get the used space of the pool.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function usedSpace(): Attribute
    {
        return Attribute::make(

            get: function ($value, $attributes) {

                $total_customer = 0;

                $where = [
                    ['ipv4pool_id', '=', $attributes['id']],
                    ['customer_id', '!=', 0],
                ];

                $total_customer = ipv4address::where($where)->count();

                return $total_customer + 1;
            },
        );
    }
}
