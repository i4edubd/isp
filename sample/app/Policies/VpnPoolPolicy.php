<?php

namespace App\Policies;

use App\Models\operator;
use App\Models\vpn_account;
use App\Models\vpn_pool;
use Illuminate\Auth\Access\HandlesAuthorization;

class VpnPoolPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(operator $operator)
    {
        if ($operator->role === 'developer') {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\vpn_pool  $vpnPool
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(operator $operator, vpn_pool $vpnPool)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(operator $operator)
    {
        if (vpn_pool::count() >= 2) {
            return false;
        }

        if ($operator->role !== 'developer') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\vpn_pool  $vpnPool
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(operator $operator, vpn_pool $vpnPool)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\vpn_pool  $vpnPool
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(operator $operator, vpn_pool $vpnPool)
    {

        if (vpn_account::count()) {
            return false;
        }

        if ($operator->role === 'developer') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\vpn_pool  $vpnPool
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(operator $operator, vpn_pool $vpnPool)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\vpn_pool  $vpnPool
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(operator $operator, vpn_pool $vpnPool)
    {
        //
    }
}
