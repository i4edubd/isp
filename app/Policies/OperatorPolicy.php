<?php

namespace App\Policies;

use App\Models\operator;
use App\Models\subscription_bill;
use Illuminate\Auth\Access\HandlesAuthorization;

class OperatorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\operator  $user
     * @return mixed
     */
    public function viewAny(operator $user)
    {
        if ($user->subscription_status == 'suspended') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function view(operator $user, operator $operator)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        // Self details view
        if ($user->id == $operator->id) {
            return true;
        }

        // You have to group admin to view details
        if ($user->id == $operator->gid) {
            return true;
        }

        // You have to master admin to view details
        if ($user->id == $operator->mgid) {
            return true;
        }

        // You have to Super admin to view details
        if ($user->id == $operator->sid) {
            return true;
        }

        // Anything Else
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\operator  $user
     * @return mixed
     */
    public function create(operator $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function update(operator $user, operator $operator)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        // Self update not permitted
        if ($user->id === $operator->id) {
            return false;
        }
        // You have to group admin to update
        if ($user->id === $operator->gid) {
            return true;
        }
        // You have to master admin to update
        if ($user->id === $operator->mgid) {
            return true;
        }
        // You have to super admin to update
        if ($user->id === $operator->sid) {
            return true;
        }
        // Anything Else
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function delete(operator $user, operator $operator)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        // Self delete not permitted
        if ($user->id === $operator->id) {
            return false;
        }
        // You have to group admin to delete
        if ($user->id === $operator->gid) {
            return true;
        }
        // Anything Else
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function restore(operator $user, operator $operator)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function forceDelete(operator $user, operator $operator)
    {
        //
    }



    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function editLimit(operator $user, operator $operator)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        // Self update not permitted
        if ($user->id === $operator->id) {
            return false;
        }
        // You have to group admin to update
        if ($user->id === $operator->gid && $operator->account_type === 'credit') {
            return true;
        }
        // Anything Else
        return false;
    }



    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function addBalance(operator $user, operator $operator)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        // Self update not permitted
        if ($user->id === $operator->id) {
            return false;
        }

        // Reseller to Sub Reseller
        if ($user->role === 'operator' && $user->account_type === 'debit' && $user->account_balance < 1) {
            return false;
        }

        // You have to group admin to update
        if ($user->id === $operator->gid && $operator->account_type === 'debit') {
            return true;
        }

        // Anything Else
        return false;
    }


    /**
     * Determine whether the user can entry cash received
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function entryCashReceived(operator $user, operator $operator)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        // Self update not permitted
        if ($user->id === $operator->id) {
            return false;
        }

        if ($operator->account_type !== 'credit') {
            return false;
        }

        // user owns the account that provided by the reseller
        if ($operator->accountsProvides->where('account_owner', $user->id)->count()) {
            return true;
        }

        // Anything Else
        return false;
    }


    /**
     * Determine whether the user can view Account Ledger
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function viewAccountLedger(operator $user, operator $operator)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        // Self update not permitted
        if ($user->id === $operator->id) {
            return false;
        }

        // reseller owns the account that provided by user/ISP
        if ($operator->account_type == 'debit') {
            if ($operator->accountsOwns->where('account_provider', $user->id)->count()) {
                return true;
            }
        }

        // user/ISP owns the account that provided by the reseller
        if ($operator->account_type == 'credit') {
            if ($operator->accountsProvides->where('account_owner', $user->id)->count()) {
                return true;
            }
        }

        // Anything Else
        return false;
    }


    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function assignPackages(operator $user, operator $operator)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        // Self package update permitted for Group Admin
        if ($user->id === $operator->id && $user->role === 'group_admin') {
            return true;
        }
        // You have to group admin to update operators package
        if ($user->id === $operator->gid && $operator->role === 'operator') {
            return true;
        }
        // You have to group admin to update sub operators package
        if ($user->id === $operator->gid && $operator->role === 'sub_operator') {
            return true;
        }
        // Anything Else
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function assignProfiles(operator $user, operator $operator)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        // Self assing not permitted
        if ($user->id === $operator->id) {
            return false;
        }
        // You have to group admin to update operators profile
        if ($user->id === $operator->gid && $operator->role === 'operator') {
            return true;
        }
        // You have to group admin to update operators profile
        if ($user->id === $operator->gid && $operator->role === 'sub_operator') {
            return true;
        }
        // Anything Else
        return false;
    }


    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function assignSpecialPermission(operator $user, operator $operator)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        // Self assing not permitted
        if ($user->id === $operator->id) {
            return false;
        }
        // You have to group admin to assign permissions
        if ($user->id === $operator->gid && $operator->role === 'operator') {
            return true;
        }
        // Anything Else
        return false;
    }


    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function getAccess(operator $user, operator $operator)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        // Self access not permitted
        if ($user->id === $operator->id) {
            return false;
        }
        // You have to group admin to access operators panel
        if ($user->id === $operator->gid) {
            return true;
        }
        // developer can access panel
        if ($user->role === 'developer') {
            return true;
        }
        // Anything Else
        return false;
    }


    /**
     * Determine whether the user can Suspend the model.
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function suspend(operator $user, operator $operator)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        // Self Suspend not permitted
        if ($user->id === $operator->id) {
            return false;
        }
        // You have to group admin to Suspend operator
        if ($user->id === $operator->gid) {
            if ($operator->role === 'operator' && $operator->status === 'active') {
                return true;
            }
        }
        // Anything Else
        return false;
    }


    /**
     * Determine whether the user can Activate the model.
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function activate(operator $user, operator $operator)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        // Self Activate not permitted
        if ($user->id === $operator->id) {
            return false;
        }
        // You have to group admin to Activate operator
        if ($user->id === $operator->gid) {
            if ($operator->role === 'operator' && $operator->status === 'suspended') {
                return true;
            }
        }
        // Anything Else
        return false;
    }


    /**
     * Determine whether the user can Suspend the model.
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function suspendSubscription(operator $user, operator $operator)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        // Self Suspend not permitted
        if ($user->id === $operator->id) {
            return false;
        }

        // You have to Super Admin to Suspend operator
        if ($user->id === $operator->sid) {
            if ($operator->role === 'group_admin' && $operator->subscription_status === 'active' && subscription_bill::where('mgid', $operator->id)->get()->count()) {
                return true;
            }
        }

        // Anything Else
        return false;
    }


    /**
     * Determine whether the user can Activate the model.
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function activateSubscription(operator $user, operator $operator)
    {
        // Self Activate not permitted
        if ($user->id === $operator->id) {
            return false;
        }

        // You have to Super Admin to Activate operator
        if ($user->id === $operator->sid) {
            if ($operator->role === 'group_admin' && $operator->subscription_status === 'suspended') {
                return true;
            }
        }
        // Anything Else
        return false;
    }

    /**
     * Determine whether the user can edit company
     *
     * @param  \App\Models\operator  $user
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function editCompany(operator $user, operator $operator)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($user->role == 'group_admin' && $user->id == $operator->id) {
            return true;
        }

        return false;
    }
}
