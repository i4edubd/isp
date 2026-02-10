<?php

namespace App\Policies;

use App\Models\operator;
use App\Models\vat_profile;
use Illuminate\Auth\Access\HandlesAuthorization;

class VatProfilePolicy
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
        if ($operator->role == 'group_admin') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\vat_profile  $vatProfile
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(operator $operator, vat_profile $vatProfile)
    {
        if ($operator->id == $vatProfile->mgid) {
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
        if ($operator->role == 'group_admin') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\vat_profile  $vatProfile
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(operator $operator, vat_profile $vatProfile)
    {
        if ($operator->id == $vatProfile->mgid) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\vat_profile  $vatProfile
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(operator $operator, vat_profile $vatProfile)
    {
        if ($operator->id == $vatProfile->mgid) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\vat_profile  $vatProfile
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(operator $operator, vat_profile $vatProfile)
    {
        if ($operator->id == $vatProfile->mgid) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\vat_profile  $vatProfile
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(operator $operator, vat_profile $vatProfile)
    {
        if ($operator->id == $vatProfile->mgid) {
            return true;
        }

        return false;
    }
}
