<?php

namespace App\Policies;

use App\Models\expense;
use App\Models\operator;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpensePolicy
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
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->role === 'manager') {
            if ($operator->permissions->contains('expense-management')) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\expense  $expense
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(operator $operator, expense $expense)
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
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->role === 'manager') {
            if ($operator->permissions->contains('expense-management')) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\expense  $expense
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(operator $operator, expense $expense)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->id === $expense->operator_id) {
            return true;
        }

        if ($operator->role === 'manager') {
            if ($operator->permissions->contains('expense-management')) {
                if ($operator->gid === $expense->operator_id) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\expense  $expense
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(operator $operator, expense $expense)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->id == $expense->operator_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\expense  $expense
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(operator $operator, expense $expense)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\expense  $expense
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(operator $operator, expense $expense)
    {
        //
    }
}
