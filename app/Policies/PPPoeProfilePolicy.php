<?php

namespace App\Policies;

use App\Models\operator;
use App\Models\pppoe_profile;
use Illuminate\Auth\Access\HandlesAuthorization;

class PPPoeProfilePolicy
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
     * @param  \App\Models\pppoe_profile  $pppoeProfile
     * @return mixed
     */
    public function view(operator $operator, pppoe_profile $pppoeProfile)
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
     * @param  \App\Models\pppoe_profile  $pppoeProfile
     * @return mixed
     */
    public function update(operator $operator, pppoe_profile $pppoeProfile)
    {
        if ($operator->id === $pppoeProfile->mgid) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\pppoe_profile  $pppoeProfile
     * @return mixed
     */
    public function delete(operator $operator, pppoe_profile $pppoeProfile)
    {
        if ($operator->id !== $pppoeProfile->mgid) {
            return false;
        }

        if ($pppoeProfile->master_packages->count()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\pppoe_profile  $pppoeProfile
     * @return mixed
     */
    public function restore(operator $operator, pppoe_profile $pppoeProfile)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\pppoe_profile  $pppoeProfile
     * @return mixed
     */
    public function forceDelete(operator $operator, pppoe_profile $pppoeProfile)
    {
        //
    }
}
