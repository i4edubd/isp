<?php

namespace App\Observers;

use App\Http\Controllers\CacheController;
use App\Models\customer_change_log;
use App\Models\Freeradius\customer;

class CustomerObserver
{
    /**
     * Handle the customer "created" event.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return void
     */
    public function created(customer $customer)
    {
        CacheController::updateCustomersList($customer);
    }

    /**
     * Handle the customer "updated" event.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return void
     */
    public function updated(customer $customer)
    {
        $changes = [];

        $update_cache = 0;

        // operator_id
        if ($customer->wasChanged('operator_id')) {
            $changes['operator change'] = "operator was changed from " . $customer->getOriginal('operator_id') . " to " . $customer->operator_id;
        }

        // billing_type
        if ($customer->wasChanged('billing_type')) {
            $changes['Billing Type change'] = "Billing type has been updated from " . $customer->getOriginal('billing_type') . " to " . $customer->billing_type;
        }

        // mobile
        if ($customer->wasChanged('mobile')) {
            $changes['mobile change'] = "Mobile number has been updated from" . $customer->getOriginal('mobile') . " to " . $customer->mobile;
            $update_cache = 1;
        }

        // billing_profile_id
        if ($customer->wasChanged('billing_profile_id')) {
            $changes['billing_profile_id change'] = "billing_profile_id has been updated from " . $customer->getOriginal('billing_profile_id') . " to " . $customer->billing_profile_id;
        }

        // username
        if ($customer->wasChanged('username')) {
            $changes['username change'] = "username was changed from " . $customer->getOriginal('username') . " to " . $customer->username;
            $update_cache = 1;
        }

        // password
        if ($customer->wasChanged('password')) {
            $changes['password change'] = "password was changed from " . $customer->getOriginal('password') . " to " . $customer->password;
        }

        // package_id
        if ($customer->wasChanged('package_id')) {
            $changes['package_id change'] = "Your package has been updated. " . $customer->getOriginal('package_id') . '::' . $customer->getOriginal('package_name') . " To  $customer->package_id :: $customer->package_name";
            $update_cache = 1;
        }

        // payment_status
        if ($customer->wasChanged('payment_status')) {
            $changes['payment_status change'] = "The payment_status has been updated from " . $customer->getOriginal('payment_status') . " to " . $customer->payment_status;
            $update_cache = 1;
        }

        // status
        if ($customer->wasChanged('status')) {
            $changes['status change'] = "Your account status has been successfully updated from " . $customer->getOriginal('status') . " to " . $customer->status;
            $update_cache = 1;
        }

        // login_ip
        if ($customer->wasChanged('login_ip')) {
            $changes['IP change'] = "IP address was altered from " . $customer->getOriginal('login_ip') . " to " . $customer->login_ip;
        }

        // package_expired_at
        if ($customer->wasChanged('package_expired_at')) {
            $changes['Expiration Time'] = "Expiration time has been modified from " . $customer->getOriginal('package_expired_at') . " to " . $customer->package_expired_at;
            $update_cache = 1;
        }

        if (auth()->user()) {
            $changed_by = auth()->user()->id . '::' . auth()->user()->role;
        } else {
            $changed_by = $customer->id . '::' . 'customer/system';
        }

        foreach ($changes as $key => $value) {
            $model = new customer_change_log();
            $model->setConnection($customer->getConnectionName());
            $model->gid = $customer->gid;
            $model->operator_id = $customer->operator_id;
            $model->customer_id = $customer->id;
            $model->changed_by = $changed_by;
            $model->topic = $key;
            $model->change_log = $value;
            $model->save();
        }

        if ($update_cache) {
            CacheController::forgetCustomer($customer);
            CacheController::updateCustomersList($customer);
        }
    }

    /**
     * Handle the customer "deleted" event.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return void
     */
    public function deleted(customer $customer)
    {
        //
    }

    /**
     * Handle the customer "restored" event.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return void
     */
    public function restored(customer $customer)
    {
        //
    }

    /**
     * Handle the customer "force deleted" event.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return void
     */
    public function forceDeleted(customer $customer)
    {
        //
    }
}
