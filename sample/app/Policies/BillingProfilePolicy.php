<?php

namespace App\Policies;

use App\Models\billing_profile;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Auth\Access\HandlesAuthorization;

class BillingProfilePolicy
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
     * @param  \App\Models\billing_profile  $billingProfile
     * @return mixed
     */
    public function view(operator $operator, billing_profile $billingProfile)
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
     * @param  \App\Models\billing_profile  $billingProfile
     * @return mixed
     */
    public function update(operator $operator, billing_profile $billingProfile)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        switch ($billingProfile->billing_type) {
            case 'Free':
                return false;
                break;
            case 'Monthly':
            case 'Daily':
                if ($operator->id == $billingProfile->mgid) {
                    return true;
                }
                break;
        }
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\billing_profile  $billingProfile
     * @return mixed
     */
    public function updatePaymentDate(operator $operator, billing_profile $billingProfile)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\billing_profile  $billingProfile
     * @return mixed
     */
    public function delete(operator $operator, billing_profile $billingProfile)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->id !== $billingProfile->mgid) {
            return false;
        }

        if (customer::where('billing_profile_id', $billingProfile->id)->count()) {
            return false;
        }

        if ($operator->id === $billingProfile->mgid) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\billing_profile  $billingProfile
     * @return mixed
     */
    public function replace(operator $operator, billing_profile $billingProfile)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->id === $billingProfile->mgid) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\billing_profile  $billingProfile
     * @return mixed
     */
    public function restore(operator $operator, billing_profile $billingProfile)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\billing_profile  $billingProfile
     * @return mixed
     */
    public function forceDelete(operator $operator, billing_profile $billingProfile)
    {
        //
    }
}
