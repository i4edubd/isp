<?php

namespace App\Policies;

use App\Models\ipv6pool;
use App\Models\operator;
use Illuminate\Auth\Access\HandlesAuthorization;

class Ipv6poolPolicy
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
     * @param  \App\Models\ipv6pool  $ipv6pool
     * @return mixed
     */
    public function view(operator $operator, ipv6pool $ipv6pool)
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
     * @param  \App\Models\ipv6pool  $ipv6pool
     * @return mixed
     */
    public function update(operator $operator, ipv6pool $ipv6pool)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\ipv6pool  $ipv6pool
     * @return mixed
     */
    public function delete(operator $operator, ipv6pool $ipv6pool)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->id !== $ipv6pool->mgid) {
            return false;
        }

        if ($ipv6pool->pppoe_profiles->count()) {
            return false;
        }

        return true;
    }


    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\ipv6pool  $ipv6pool
     * @return mixed
     */
    public function restore(operator $operator, ipv6pool $ipv6pool)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\ipv6pool  $ipv6pool
     * @return mixed
     */
    public function forceDelete(operator $operator, ipv6pool $ipv6pool)
    {
        //
    }
}
