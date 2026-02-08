<?php

namespace App\Policies;

use App\Models\customer_bill;
use App\Models\operator;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerBillPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can receive payment.
     *
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function receivePayment(operator $operator, customer_bill $customerBill)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->deny('This action is blocked because of an outstanding subscription invoice on the administrator account.');
        }

        if ($customerBill->processing == 1) {
            return $this->deny('This invoice is currently being paid.');
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        // group admin
        if ($customerBill->mgid === $operator->id) {
            return true;
        }

        // manager
        if ($operator->role === 'manager') {
            if ($operator->permissions->contains('receive-payment') && $customerBill->operator_id == $operator->gid) {
                if ($operator->group_admin->account_type == 'debit') {
                    if ($operator->group_admin->account_balance >= $customerBill->operator_amount) {
                        return true;
                    }
                } else {
                    if ($operator->group_admin->credit_limit == 0) {
                        return true;
                    }
                    if ($operator->group_admin->credit_balance >= $customerBill->operator_amount) {
                        return true;
                    }
                }
            }
        }

        // operator & sub_operator
        if ($customerBill->operator_id == $operator->id) {
            if ($operator->account_type == 'debit') {
                if ($operator->account_balance >= $customerBill->operator_amount) {
                    return true;
                }
            } else {
                if ($operator->credit_limit == 0) {
                    return true;
                }
                if ($operator->credit_balance >= $customerBill->operator_amount) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * Determine whether the user can edit Invoice.
     *
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function editInvoice(operator $operator, customer_bill $customerBill)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customerBill->mgid == $operator->id) {
            return true;
        }

        if ($customerBill->operator_id == $operator->id) {
            return $operator->permissions->contains('edit-bills');
        }

        return false;
    }


    /**
     * Determine whether the user can delete Invoice.
     *
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function deleteInvoice(operator $operator, customer_bill $customerBill)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customerBill->mgid == $operator->id) {
            return true;
        }

        if ($customerBill->operator_id == $operator->id) {
            return $operator->permissions->contains('delete-bills');
        }

        return false;
    }


    /**
     * Determine whether the user can delete Invoice.
     *
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function discount(operator $operator, customer_bill $customerBill)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customerBill->mgid == $operator->id) {
            return true;
        }

        if ($customerBill->operator_id == $operator->id) {
            return $operator->permissions->contains('discount-on-bills');
        }

        return false;
    }


    /**
     * Determine whether the user can print Invoice.
     *
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public function printInvoice(operator $operator, customer_bill $customerBill)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($operator->role === 'manager') {
            return $operator->permissions->contains('print-invoice') &&
                $customerBill->operator_id == $operator->gid;
        }

        if ($customerBill->mgid == $operator->id) {
            return true;
        }

        if ($customerBill->gid == $operator->id) {
            return true;
        }

        if ($customerBill->operator_id == $operator->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can do not bill operator
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\customer_bill  $customerBill
     * @return mixed
     */
    public function doNotBillOperator(operator $operator, customer_bill $customer_bill)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        // must be group admin
        if ($operator->role !== 'group_admin') {
            return false;
        }

        // not for self customers payment
        if ($operator->id == $customer_bill->operator_id) {
            return false;
        }

        return true;
    }


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
     * @param  \App\Models\customer_bill  $customerBill
     * @return mixed
     */
    public function view(operator $operator, customer_bill $customerBill)
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
     * @param  \App\Models\customer_bill  $customerBill
     * @return mixed
     */
    public function update(operator $operator, customer_bill $customerBill)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\customer_bill  $customerBill
     * @return mixed
     */
    public function delete(operator $operator, customer_bill $customerBill)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\customer_bill  $customerBill
     * @return mixed
     */
    public function restore(operator $operator, customer_bill $customerBill)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\customer_bill  $customerBill
     * @return mixed
     */
    public function forceDelete(operator $operator, customer_bill $customerBill)
    {
        //
    }
}
