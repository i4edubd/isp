<?php

namespace App\Policies;

use App\Models\account;
use App\Models\operator;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
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
            return $this->deny('This action is blocked because of an outstanding subscription invoice on the administrator account.');
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\account  $account
     * @return mixed
     */
    public function view(operator $operator, account $account)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($operator->id == $account->account_provider) {
            return true;
        }

        if ($operator->id == $account->account_owner) {
            return true;
        }

        return false;
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
     * @param  \App\Models\account  $account
     * @return mixed
     */
    public function update(operator $operator, account $account)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\account  $account
     * @return mixed
     */
    public function delete(operator $operator, account $account)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\account  $account
     * @return mixed
     */
    public function restore(operator $operator, account $account)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\account  $account
     * @return mixed
     */
    public function forceDelete(operator $operator, account $account)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\account  $account
     * @return mixed
     */
    public function exchange(operator $operator, account $account)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        $related_to_account = 0;

        if ($operator->id === $account->account_provider) {
            $related_to_account = 1;
        }

        if ($operator->id === $account->account_owner) {
            $related_to_account = 1;
        }

        if ($related_to_account === 0) {
            return false;
        }

        $where = [
            ['account_provider', '=', $account->account_owner],
            ['account_owner', '=', $account->account_provider],
        ];

        if (account::where($where)->count() == 0) {
            return false;
        }

        if ($account->balance < 1) {
            return false;
        }

        $exchange_account = account::where($where)->first();

        if ($exchange_account->balance < 1) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can cash out
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\account  $account
     * @return mixed
     */
    public function cashOut(operator $operator, account $account)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($account->balance < 1) {
            return false;
        }

        $roles = ['super_admin', 'group_admin', 'developer', 'operator'];

        if (in_array($operator->role, $roles) == false) {
            return false;
        }

        // should be account owner && provider should be downstream && provider should be credit account holder

        $provider = $account->provider;

        return $operator->id === $account->account_owner && $operator->id === $provider->gid && $provider->account_type === 'credit';
    }

    /**
     * Determine whether the user can send money
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\account  $account
     * @return mixed
     */
    public function sendMoney(operator $operator, account $account)
    {
        $roles = ['super_admin', 'developer'];

        if (in_array($operator->role, $roles) == false) {
            return false;
        }

        return $operator->id === $account->account_provider;
    }

    /**
     * Determine whether the user can recharge account through online
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\account  $account
     * @return mixed
     */
    public function onlineRechage(operator $operator, account $account)
    {
        // operator must be account owner
        if ($operator->id !== $account->account_owner) {
            return false;
        }

        // account owner must be reseller
        if ($operator->gid !== $account->account_provider) {
            return false;
        }

        // account owner's account_type must be debit
        if ($operator->account_type !== 'debit') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can pay credit payments through online
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\account  $account
     * @return mixed
     */
    public function onlinePayment(operator $operator, account $account)
    {
        // operator must be account provider
        if ($operator->id !== $account->account_provider) {
            return false;
        }

        // account provider must be reseller
        if ($operator->gid !== $account->account_owner) {
            return false;
        }

        // account provider's account_type must be credit
        if ($operator->account_type !== 'credit') {
            return false;
        }

        return true;
    }
}
