<?php

namespace App\Policies;

use App\Http\Controllers\Cache\PackageCacheController;
use App\Http\Controllers\CacheController;
use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Carbon\Carbon;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Cache;

class CustomerPolicy
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
            return $this->accountSuspendedMessage();
        }

        return true;
    }

    /**
     * Determine whether the user can view Details.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function viewDetails(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($operator->role === 'manager') {
            return $operator->permissions->contains('view-customer-details') && $operator->gid == $customer->operator_id;
        }

        if ($customer->gid == $operator->id) {
            return true;
        }

        if ($customer->mgid == $operator->id) {
            return true;
        }

        if ($customer->operator_id == $operator->id) {
            return self::hasBalance($operator);
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
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($operator->role === 'manager') {
            if ($operator->permissions->contains('create-customer')) {
                return self::hasBalance($operator->group_admin);
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether the user can add child customer
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function addChild(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->status == 'disabled') {
            return false;
        }

        switch ($customer->connection_type) {
            case 'PPPoE':
                if ($customer->id == $customer->parent_id) {
                    return $this->update($operator, $customer);
                }
                break;
            case 'Hotspot':
            case 'StaticIp':
            case 'Other':
                return false;
                break;
        }

        return false;
    }

    /**
     * Determine whether the user can make customer child
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function makeChild(operator $operator, customer $customer)
    {
        $key = 'CustomerPolicy_makeChild_' . $operator->id . '_' . $customer->id;

        return Cache::remember($key, 180, function () use ($operator, $customer) {

            if ($operator->subscription_status == 'suspended') {
                return false;
            }

            if ($operator->new_id !== 0) {
                return false;
            }

            if ($customer->status == 'disabled') {
                return false;
            }

            switch ($customer->connection_type) {
                case 'PPPoE':
                    if ($customer->id == $customer->parent_id) {
                        if ($customer->childAccounts()->count() == 1) {
                            return $this->update($operator, $customer);
                        }
                    }
                    break;
                case 'Hotspot':
                case 'StaticIp':
                case 'Other':
                    return false;
                    break;
            }

            return false;
        });
    }

    /**
     * Determine whether the user can make customer parent
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function makeParent(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->status == 'disabled') {
            return false;
        }

        switch ($customer->connection_type) {
            case 'PPPoE':
                if ($customer->id !== $customer->parent_id) {
                    return $this->update($operator, $customer);
                }
                break;
            case 'Hotspot':
            case 'StaticIp':
            case 'Other':
                return false;
                break;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function editIP(operator $operator, customer $customer)
    {
        if ($customer->connection_type === 'PPPoE') {
            return $this->update($operator, $customer);
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function update(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($operator->role === 'manager') {
            return $operator->permissions->contains('edit-customer') && $operator->gid == $customer->operator_id;
        }

        if ($customer->gid == $operator->id) {
            return true;
        }

        if ($customer->mgid == $operator->id) {
            return true;
        }

        if ($customer->operator_id == $operator->id) {
            return self::hasBalance($operator);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function delete(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($operator->role === 'manager') {
            return false;
        }

        if ($operator->role === 'developer') {
            return true;
        }

        if ($operator->role === 'operator') {
            if (!$operator->permissions->contains('delete-customer')) {
                return false;
            }
        }

        if ($operator->role === 'sub_operator') {
            if (!$operator->group_admin->permissions->contains('delete-customer')) {
                return false;
            }
        }

        if ($customer->gid == $operator->id) {
            return true;
        }

        if ($customer->mgid == $operator->id) {
            return true;
        }

        if ($customer->operator_id == $operator->id) {
            return self::hasBalance($operator);
        }

        return false;
    }


    /**
     * Determine whether the user can activate the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function activate(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->status === 'active') {
            return false;
        }

        switch ($customer->connection_type) {
            case 'PPPoE':
                if ($customer->billing_type == 'Daily' && Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')->lessThan(Carbon::now(getTimeZone($customer->operator_id)))) {
                    return true;
                }
                if ($customer->billing_type == 'Free') {
                    return true;
                }
                // For Montly Customer pass
                break;
            case 'Hotspot':
            case 'Other':
                return false;
                break;
            case 'StaticIp':
                // Pass
                break;
        }

        $billing_profile = CacheController::getBillingProfile($customer->billing_profile_id);

        // billed customer
        $expiration = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en');
        $now = Carbon::now(getTimeZone($customer->operator_id));
        if ($expiration->lessThan($now)) {
            // Monthly Billing (PPP & Static IP)
            if ($billing_profile->auto_bill === 'yes' && $billing_profile->auto_lock === 'yes') {
                $bill_where = [
                    ['gid', '=', $customer->gid],
                    ['operator_id', '=', $customer->operator_id],
                    ['customer_id', '=', $customer->id],
                ];
                if (customer_bill::where($bill_where)->count()) {
                    $due_date = Carbon::createFromFormat(config('app.date_format'), $billing_profile->payment_date, getTimeZone($customer->operator_id));
                    if ($due_date->lessThan($now)) {
                        return false;
                    }
                }
            }
        }

        if ($operator->role === 'manager') {
            if ($operator->permissions->contains('activate-customer')) {
                return self::hasBalance($operator->group_admin);
            } else {
                return false;
            }
        }

        if ($customer->operator_id == $operator->id) {
            return self::hasBalance($operator);
        }

        if ($customer->gid == $operator->id) {
            return true;
        }

        if ($customer->mgid == $operator->id) {
            return true;
        }

        return false;
    }


    /**
     * Determine whether the user can suspend the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function suspend(operator $operator, customer $customer)
    {
        if ($customer->status === 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        switch ($customer->connection_type) {
            case 'PPPoE':
            case 'StaticIp':
                if ($customer->billing_type == 'Daily') {
                    return true;
                }
                if ($customer->billing_type == 'Free') {
                    return false;
                }
                if ($operator->role === 'manager') {
                    return $operator->permissions->contains('suspend-customer');
                }
                if ($customer->gid == $operator->id) {
                    return true;
                }
                if ($customer->mgid == $operator->id) {
                    return true;
                }
                if ($customer->operator_id == $operator->id) {
                    return self::hasBalance($operator);
                }
                break;
            case 'Hotspot':
            case 'Other':
                return false;
                break;
        }

        return false;
    }


    /**
     * Determine whether the user can disable the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function disable(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->status === 'disabled') {
            return false;
        }
        switch ($customer->connection_type) {
            case 'PPPoE':
            case 'StaticIp':
                if ($customer->billing_type == 'Daily') {
                    return true;
                }
                if ($customer->billing_type == 'Free') {
                    return false;
                }
                if ($operator->role === 'manager') {
                    return $operator->permissions->contains('disable-customer');
                }
                if ($customer->gid == $operator->id) {
                    return true;
                }
                if ($customer->mgid == $operator->id) {
                    return true;
                }
                if ($customer->operator_id == $operator->id) {
                    return self::hasBalance($operator);
                }
                break;
            case 'Hotspot':
            case 'Other':
                return false;
                break;
        }

        return false;
    }

    /**
     * Determine whether the user can change package.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function editPackageWithoutAccounting(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($operator->id == $customer->mgid) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can change package.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function changePackage(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->status == 'disabled') {
            return false;
        }

        switch ($customer->connection_type) {
            case 'PPPoE':
            case 'StaticIp':
            case 'Other':
                if ($customer->billing_type === 'Daily') {
                    return false;
                }
                if ($operator->role === 'manager') {
                    if ($operator->permissions->contains('change-customer-package')) {
                        return self::hasBalance($operator->group_admin);
                    } else {
                        return false;
                    }
                }
                if ($customer->gid == $operator->id) {
                    return true;
                }
                if ($customer->mgid == $operator->id) {
                    return true;
                }
                if ($customer->operator_id == $operator->id) {
                    return self::hasBalance($operator);
                }
                break;
            case 'Hotspot':
                return false;
                break;
        }

        return false;
    }

    /**
     * Determine whether the user can change package.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function dailyRecharge(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->status == 'disabled') {
            return false;
        }

        if (is_null($customer->last_recharge_time) == false) {
            if (Carbon::createFromFormat(config('app.db_dateTime_format'), $customer->last_recharge_time, getTimeZone($operator->id))->greaterThan(Carbon::now(getTimeZone($operator->id))->subMinutes(10))) {
                return false;
            }
        }

        switch ($customer->connection_type) {
            case 'PPPoE':
            case 'StaticIp':
            case 'Other':
                if ($customer->billing_type === 'Daily') {
                    if ($operator->role === 'manager') {
                        if ($operator->permissions->contains('change-customer-package')) {
                            return self::hasBalance($operator->group_admin);
                        } else {
                            return false;
                        }
                    }
                    if ($customer->gid == $operator->id) {
                        return true;
                    }
                    if ($customer->mgid == $operator->id) {
                        return true;
                    }
                    if ($customer->operator_id == $operator->id) {
                        return self::hasBalance($operator);
                    }
                }
                break;
            case 'Hotspot':
                return false;
                break;
        }

        return false;
    }

    /**
     * Determine whether the user can change package.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function hotspotRecharge(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->status == 'disabled') {
            return false;
        }

        if (is_null($customer->last_recharge_time) == false) {
            if (Carbon::createFromFormat(config('app.db_dateTime_format'), $customer->last_recharge_time, getTimeZone($operator->id))->greaterThan(Carbon::now(getTimeZone($operator->id))->subMinutes(10))) {
                return false;
            }
        }

        switch ($customer->connection_type) {
            case 'PPPoE':
            case 'StaticIp':
            case 'Other':
                return false;
                break;
            case 'Hotspot':
                if ($operator->role === 'manager') {
                    if ($operator->permissions->contains('change-customer-package')) {
                        return self::hasBalance($operator->group_admin);
                    } else {
                        return false;
                    }
                }
                if ($customer->gid == $operator->id) {
                    return true;
                }
                if ($customer->mgid == $operator->id) {
                    return true;
                }
                if ($customer->operator_id == $operator->id) {
                    return self::hasBalance($operator);
                }
                break;
        }

        return false;
    }

    /**
     * Determine whether the user can edit speed limit.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function editSpeedLimit(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->status == 'disabled') {
            return false;
        }

        switch ($customer->connection_type) {
            case 'PPPoE':
            case 'StaticIp':
            case 'Other':
                return false;
                break;
            case 'Hotspot':
                if ($customer->mgid === $operator->id) {
                    return true;
                }
                break;
        }

        return false;
    }


    /**
     * Determine whether the user can generate Bill.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function generateBill(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->connection_type == 'Hotspot') {
            return false;
        }

        if ($customer->status !== 'active') {
            return false;
        }

        if ($customer->billing_type === 'Daily') {
            return false;
        }

        if ($operator->role === 'manager') {
            return $operator->permissions->contains('generate-bill') && $operator->gid == $customer->operator_id;
        }

        if ($customer->gid == $operator->id) {
            return true;
        }

        if ($customer->mgid == $operator->id) {
            return true;
        }

        if ($customer->operator_id == $operator->id) {
            return self::hasBalance($operator);
        }

        return false;
    }


    /**
     * Determine whether the user can remove Mac Bind.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function removeMacBind(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->status == 'disabled') {
            return false;
        }

        if ($customer->mac_bind == 0) {
            return false;
        }

        if ($operator->role === 'manager') {
            return $operator->permissions->contains('remove-mac-bind');
        }

        if ($customer->gid == $operator->id) {
            return true;
        }

        if ($customer->mgid == $operator->id) {
            return true;
        }

        if ($customer->operator_id == $operator->id) {
            return self::hasBalance($operator);
        }

        return false;
    }


    /**
     * Determine whether the user can send Sms.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function sendSms(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->status == 'disabled') {
            return false;
        }

        if ($operator->role === 'manager') {
            return $operator->permissions->contains('send-sms');
        }

        if ($customer->gid == $operator->id) {
            return true;
        }

        if ($customer->mgid == $operator->id) {
            return true;
        }

        if ($customer->operator_id == $operator->id) {
            return self::hasBalance($operator);
        }

        return false;
    }

    /**
     * Determine whether the user can send Sms.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function sendLink(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->status == 'disabled') {
            return false;
        }

        if ($customer->payment_status == 'paid') {
            return false;
        }

        if ($operator->role === 'manager') {
            return $operator->permissions->contains('send-sms');
        }

        if ($customer->gid == $operator->id) {
            return true;
        }

        if ($customer->mgid == $operator->id) {
            return true;
        }

        if ($customer->operator_id == $operator->id) {
            return self::hasBalance($operator);
        }

        return false;
    }


    /**
     * Determine whether the operator can chage operator
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function changeOperator(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->connection_type == 'StaticIp') {
            return false;
        }

        if ($customer->mgid == $operator->id) {
            return true;
        }

        if ($customer->gid == $operator->id && $operator->role == 'operator') {
            return true;
        }

        return false;
    }


    /**
     * Determine whether the user can pay advance payment.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function advancePayment(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->status !== 'active') {
            return false;
        }

        switch ($customer->connection_type) {
            case 'PPPoE':
            case 'StaticIp':
            case 'Other':
                if ($customer->billing_type === 'Daily') {
                    return false;
                }
                if ($customer->gid == $operator->id) {
                    return true;
                }
                if ($customer->mgid == $operator->id) {
                    return true;
                }
                if ($customer->operator_id == $operator->id) {
                    return self::hasBalance($operator);
                }
                break;
            case 'Hotspot':
                return false;
                break;
        }

        return false;
    }


    /**
     * Determine whether the user can activate the fup.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function activateFup(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->connection_type !== 'PPPoE') {
            return false;
        }

        if ($customer->status !== 'active') {
            return false;
        }

        $package = PackageCacheController::getPackage($customer->package_id);

        if (!$package) {
            return false;
        }

        $master_package = $package->master_package;

        if (!$master_package) {
            return false;
        }

        if (!$master_package->fair_usage_policy) {
            return false;
        }

        if ($customer->gid == $operator->id) {
            return true;
        }

        if ($customer->mgid == $operator->id) {
            return true;
        }

        if ($customer->operator_id == $operator->id) {
            return self::hasBalance($operator);
        }

        return false;
    }

    /**
     * Determine whether the user can download Internet History
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function downloadInternetHistory(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        switch ($customer->connection_type) {
            case 'PPPoE':
            case 'Hotspot':
                if ($operator->role == 'manager') {
                    if ($operator->gid == $customer->operator_id) {
                        return true;
                    }
                }
                if ($customer->gid == $operator->id) {
                    return true;
                }
                if ($customer->mgid == $operator->id) {
                    return true;
                }
                if ($customer->operator_id == $operator->id) {
                    return self::hasBalance($operator);
                }
                break;
            case 'StaticIp':
            case 'Other':
                return false;
                break;
        }

        return false;
    }

    /**
     * Determine whether the user can create custom price.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function customPrice(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->status !== 'active') {
            return false;
        }

        if ($customer->connection_type == 'Hotspot') {
            return false;
        }

        if ($customer->mgid == $operator->id) {
            return true;
        }

        if ($customer->operator_id == $operator->id) {
            if ($operator->permissions->contains('set-special-price-for-customer')) {
                return self::hasBalance($operator);
            }
        }

        return false;
    }

    /**
     * Determine whether the operator can edit suspend date
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function editSuspendDate(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        switch ($customer->connection_type) {
            case 'PPPoE':
            case 'StaticIp':
                if ($customer->mgid == $operator->id) {
                    return true;
                }
                if ($operator->role === 'manager') {
                    return $operator->group_admin->role === 'group_admin';
                }
                break;
            case 'Hotspot':
            case 'Other':
                return false;
                break;
        }
        return false;
    }

    /**
     * Determine whether the operator can reschedule Payment Date
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function viewActivateOptions(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->status === 'active') {
            return false;
        }

        if ($customer->payment_status === 'paid') {
            return false;
        }

        switch ($customer->connection_type) {
            case 'PPPoE':
            case 'StaticIp':
                if ($customer->billing_type === 'Daily' || $customer->billing_type === 'Free') {
                    return false;
                }
                if ($customer->status === 'suspended' && $customer->payment_status === 'billed') {
                    return true;
                }
                break;
            case 'Hotspot':
            case 'Other':
                return false;
                break;
        }

        return false;
    }

    /**
     * Determine whether the user can edit Billing Profile.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function editBillingProfile(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        switch ($customer->connection_type) {
            case 'PPPoE':
            case 'StaticIp':
            case 'Other':
                if ($operator->role == 'manager') {
                    if ($operator->gid == $customer->operator_id) {
                        return true;
                    }
                }
                if ($customer->gid == $operator->id) {
                    return true;
                }
                if ($customer->mgid == $operator->id) {
                    return true;
                }
                if ($customer->operator_id == $operator->id) {
                    return self::hasBalance($operator);
                }
                break;
            case 'Hotspot':
                return false;
                break;
        }

        return false;
    }

    /**
     * Determine whether the user can disconnect the model.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\Freeradius\customer  $customer
     * @return mixed
     */
    public function disconnect(operator $operator, customer $customer)
    {
        if ($operator->subscription_status == 'suspended') {
            return $this->accountSuspendedMessage();
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($customer->connection_type !== 'PPPoE') {
            return false;
        }

        if ($customer->mgid == $operator->id) {
            return true;
        }

        if ($customer->operator_id == $operator->id) {
            return true;
        }

        return false;
    }

    /**
     * Check Group Admin's Account Balance.
     *
     * @param  \App\Models\operator  $operator
     * @return mixed
     */
    public static function hasBalance(operator $operator)
    {
        if ($operator->subscription_status == 'suspended') {
            return false;
        }

        if ($operator->new_id !== 0) {
            return false;
        }

        if ($operator->role == 'group_admin') {
            return true;
        }

        $account = CacheController::getResellerAccount($operator);
        if (!$account) {
            return true;
        }

        if ($operator->account_type === 'debit') {
            if ($account->balance > 1) {
                return true;
            } else {
                return false;
            }
        }

        if ($operator->account_type === 'credit') {
            if ($operator->credit_limit > 1) {
                if (($operator->credit_limit - $account->balance) > 1) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * account suspended message.
     *
     * @return mixed
     */
    public function accountSuspendedMessage()
    {
        return $this->deny('This action is blocked because of an outstanding subscription invoice on the administrator account.');
    }
}
