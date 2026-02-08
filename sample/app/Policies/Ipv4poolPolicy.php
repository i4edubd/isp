<?php

namespace App\Policies;

use App\Models\fair_usage_policy;
use App\Models\ipv4pool;
use App\Models\operator;
use Illuminate\Auth\Access\HandlesAuthorization;

class Ipv4poolPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function viewAny(operator $operator)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\ipv4pool  $ipv4pool
     * @return mixed
     */
    public function view(operator $operator, ipv4pool $ipv4pool)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function create(operator $operator)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\ipv4pool  $ipv4pool
     * @return mixed
     */
    public function update(operator $operator, ipv4pool $ipv4pool)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\ipv4pool  $ipv4pool
     * @return mixed
     */
    public function delete(operator $operator, ipv4pool $ipv4pool)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->id !== $ipv4pool->mgid) {
            return false;
        }

        if ($ipv4pool->name == 'suspended_users_pool') {
            return false;
        }

        if ($ipv4pool->pppoe_profiles->count()) {
            return false;
        }

        if (fair_usage_policy::where('ipv4pool_id', $ipv4pool->id)->count()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\ipv4pool  $ipv4pool
     * @return mixed
     */
    public function restore(operator $operator, ipv4pool $ipv4pool)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\ipv4pool  $ipv4pool
     * @return mixed
     */
    public function forceDelete(operator $operator, ipv4pool $ipv4pool)
    {
        //
    }


    /**
     * Determine whether the user can change pool name.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\ipv4pool  $ipv4pool
     * @return mixed
     */
    public function changeName(operator $operator, ipv4pool $ipv4pool)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->id !== $ipv4pool->mgid) {
            return false;
        }

        if ($ipv4pool->name == 'suspended_users_pool') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can replace pool.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\ipv4pool  $ipv4pool
     * @return mixed
     */
    public function replace(operator $operator, ipv4pool $ipv4pool)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->id !== $ipv4pool->mgid) {
            return false;
        }

        if ($ipv4pool->name == 'suspended_users_pool') {
            return false;
        }

        return true;
    }
}
