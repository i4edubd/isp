<?php

namespace App\Policies;

use App\Http\Controllers\CacheController;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\package;
use Illuminate\Auth\Access\HandlesAuthorization;

class PackagePolicy
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
     * @param  \App\Models\package  $package
     * @return mixed
     */
    public function view(operator $operator, package $package)
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
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\package  $package
     * @return mixed
     */
    public function update(operator $operator, package $package)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        // Trial is constant
        if ($package->name == 'Trial') {
            return false;
        }

        // Master Admin Can
        if ($operator->id === $package->mgid) {
            return true;
        }

        // Group Admin Can
        if ($operator->id == $package->gid) {
            return true;
        }

        // Operator Can
        if ($operator->id == $package->operator_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\package  $package
     * @return mixed
     */
    public function updateName(operator $operator, package $package)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        // Trial is constant
        if ($package->name == 'Trial') {
            return false;
        }

        // Master Admin Can
        if ($operator->id === $package->mgid) {
            return true;
        }

        // Group Admin Can
        if ($operator->id == $package->gid) {
            return true;
        }

        // Operator Can not
        if ($operator->id == $package->operator_id) {
            return false;
        }

        return false;
    }


    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\package  $package
     * @return mixed
     */
    public function updatePrice(operator $operator, package $package)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        // Trial is constant
        if ($package->name == 'Trial') {
            return false;
        }

        // Master Admin Can
        if ($operator->id === $package->mgid) {
            return true;
        }

        // Group Admin Can
        if ($operator->id == $package->gid) {
            return true;
        }

        // Operator Can
        if ($operator->id == $package->operator_id) {
            return $operator->permissions->contains('edit-package-price');
        }

        return false;
    }


    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\package  $package
     * @return mixed
     */
    public function updateOperatorPrice(operator $operator, package $package)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        // Trial is constant
        if ($package->name == 'Trial') {
            return false;
        }

        // Master Admin Can
        if ($operator->id === $package->mgid) {
            return true;
        }

        // Group Admin Can
        if ($operator->id == $package->gid) {
            return true;
        }

        return false;
    }


    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\package  $package
     * @return mixed
     */
    public function replace(operator $operator, package $package)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        // Trial is constant
        if ($package->name == 'Trial') {
            return false;
        }

        if ($package->customer_count == 0) {
            return false;
        }

        // Master Admin Can
        if ($operator->id === $package->mgid) {
            return true;
        }

        // Group Admin Can
        if ($operator->id == $package->gid) {
            return true;
        }

        return false;
    }


    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\package  $package
     * @return mixed
     */
    public function delete(operator $operator, package $package)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        // Trial is constant
        if ($package->name == 'Trial') {
            return false;
        }

        if ($package->customer_count > 0) {
            return false;
        }

        foreach ($package->child_packages as $child_package) {
            if ($child_package->customer_count > 0) {
                return false;
            }
        }

        // Master Admin Can
        if ($operator->id === $package->mgid) {
            return true;
        }

        // Group Admin Can
        if ($operator->id == $package->gid) {
            return true;
        }

        // Operator Can
        if ($operator->id == $package->operator_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\package  $package
     * @return mixed
     */
    public function restore(operator $operator, package $package)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\package  $package
     * @return mixed
     */
    public function forceDelete(operator $operator, package $package)
    {
        //
    }

    /**
     * Determine whether the user can purchase the package.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @param  \App\Models\package  $package
     * @return mixed
     */
    public function purchase(customer $customer, package $package)
    {
        $operator = CacheController::getOperator($customer->operator_id);

        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($customer->payment_status == 'billed') {
            return false;
        }

        if ($customer->connection_type !== $package->master_package->connection_type) {
            return false;
        }

        switch ($customer->connection_type) {
            case 'PPPoE':
            case 'StaticIp':
            case 'Other':
                return false;
                break;

            case 'Hotspot':
                return true;
                break;
        }

        return false;
    }
}
