<?php

namespace App\Policies;

use App\Models\operator;
use App\Models\temp_billing_profile;
use Illuminate\Auth\Access\HandlesAuthorization;

class TempBillingProfilePolicy
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
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\temp_billing_profile  $tempBillingProfile
     * @return mixed
     */
    public function view(operator $operator, temp_billing_profile $tempBillingProfile)
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
     * @param  \App\Models\temp_billing_profile  $tempBillingProfile
     * @return mixed
     */
    public function update(operator $operator, temp_billing_profile $tempBillingProfile)
    {
        if ($operator->id == $tempBillingProfile->mgid) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\temp_billing_profile  $tempBillingProfile
     * @return mixed
     */
    public function delete(operator $operator, temp_billing_profile $tempBillingProfile)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\temp_billing_profile  $tempBillingProfile
     * @return mixed
     */
    public function restore(operator $operator, temp_billing_profile $tempBillingProfile)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\temp_billing_profile  $tempBillingProfile
     * @return mixed
     */
    public function forceDelete(operator $operator, temp_billing_profile $tempBillingProfile)
    {
        //
    }
}
