<?php

namespace App\Policies;

use App\Models\operator;
use App\Models\vpn_account;
use Illuminate\Auth\Access\HandlesAuthorization;

class VpnAccountPolicy
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
        if (config('consumer.vpn_service') == false) {
            return false;
        }

        if (config('consumer.demo_gid') == $operator->id) {
            return false;
        }

        if ($operator->role == 'group_admin') {
            return true;
        }

        if ($operator->role == 'developer') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\vpn_account  $vpnAccount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(operator $operator, vpn_account $vpnAccount)
    {
        if (config('consumer.vpn_service') == false) {
            return false;
        }

        if (config('consumer.demo_gid') == $operator->id) {
            return false;
        }

        if ($operator->role == 'developer') {
            return true;
        }

        if ($operator->id === $vpnAccount->mgid) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(operator $operator)
    {
        if (config('consumer.vpn_service') == false) {
            return false;
        }

        if (config('consumer.demo_gid') == $operator->id) {
            return false;
        }

        if ($operator->role == 'group_admin') {
            return true;
        }

        if ($operator->role == 'developer') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\vpn_account  $vpnAccount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(operator $operator, vpn_account $vpnAccount)
    {
        if (config('consumer.vpn_service') == false) {
            return false;
        }

        if (config('consumer.demo_gid') == $operator->id) {
            return false;
        }

        if ($operator->role == 'developer') {
            return true;
        }

        if ($operator->id === $vpnAccount->mgid) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\vpn_account  $vpnAccount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(operator $operator, vpn_account $vpnAccount)
    {
        if (config('consumer.vpn_service') == false) {
            return false;
        }

        if (config('consumer.demo_gid') == $operator->id) {
            return false;
        }

        if ($operator->role == 'developer') {
            return true;
        }

        if ($operator->id === $vpnAccount->mgid) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\vpn_account  $vpnAccount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(operator $operator, vpn_account $vpnAccount)
    {
        if (config('consumer.vpn_service') == false) {
            return false;
        }

        if (config('consumer.demo_gid') == $operator->id) {
            return false;
        }

        if ($operator->role == 'developer') {
            return true;
        }

        if ($operator->id === $vpnAccount->mgid) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\vpn_account  $vpnAccount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(operator $operator, vpn_account $vpnAccount)
    {
        if (config('consumer.vpn_service') == false) {
            return false;
        }

        if (config('consumer.demo_gid') == $operator->id) {
            return false;
        }

        if ($operator->role == 'developer') {
            return true;
        }

        if ($operator->id === $vpnAccount->mgid) {
            return true;
        }

        return false;
    }
}
