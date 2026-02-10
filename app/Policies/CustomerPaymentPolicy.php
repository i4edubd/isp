<?php

namespace App\Policies;

use App\Models\customer_payment;
use App\Models\operator;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPaymentPolicy
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
     * @param  \App\Models\customer_payment  $customerPayment
     * @return mixed
     */
    public function view(operator $operator, customer_payment $customerPayment)
    {
        if ($customerPayment->mgid == $operator->id) {
            return true;
        }

        if ($customerPayment->gid == $operator->id) {
            return true;
        }

        if ($customerPayment->operator_id == $operator->id) {
            return true;
        }

        if ($operator->role == 'manager') {
            if ($customerPayment->operator_id == $operator->gid) {
                return true;
            }
        }

        if ($operator->role == 'super_admin') {
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
     * @param  \App\Models\customer_payment  $customerPayment
     * @return mixed
     */
    public function update(operator $operator, customer_payment $customerPayment)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($customerPayment->mgid == $operator->id) {
            return true;
        }

        if ($customerPayment->operator_id == $operator->id) {
            return $operator->permissions->contains('edit-customer-payment');
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\customer_payment  $customerPayment
     * @return mixed
     */
    public function delete(operator $operator, customer_payment $customerPayment)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($customerPayment->mgid == $operator->id) {
            return true;
        }

        if ($customerPayment->operator_id == $operator->id) {
            return $operator->permissions->contains('delete-customer-payment');
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\customer_payment  $customerPayment
     * @return mixed
     */
    public function restore(operator $operator, customer_payment $customerPayment)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\customer_payment  $customerPayment
     * @return mixed
     */
    public function forceDelete(operator $operator, customer_payment $customerPayment)
    {
        //
    }
}
