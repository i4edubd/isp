<?php

namespace App\Policies;

use App\Http\Controllers\enum\PaymentPurpose;
use App\Models\Freeradius\customer;
use Illuminate\Auth\Access\HandlesAuthorization;

class CanCustomerPayPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @param  \App\Models\Freeradius\customer $customer
     * @param  string $paymentPurpose;
     * @return void
     */
    public function __construct(customer $customer, PaymentPurpose $paymentPurpose)
    {

        switch ($paymentPurpose->name) {
            case 'PACKAGE_PURCHASE':
            case 'PACKAGE_CHANGE':
            case 'PACKAGE_DOWNGRADE':
            case 'PACKAGE_UPGRADE':
            case 'NEW_CUSTOMER':
                return match ($customer->type) {
                    'PPP_FREE', 'HOTSPOT_FREE', 'STATIC_FREE', 'OTHER_FREE', 'HOTPOST_MONTHLY', 'STATIC_DAILY', 'OTHER_DAILY'  => match ($customer->overall_status) {
                        default => false
                    },

                    'PPP_DAILY', 'PPP_MONTHLY', 'HOTPOST_DAILY', 'STATIC_MONTHLY', 'OTHER_MONTHLY' => match ($customer->overall_status) {
                        default => true
                    },
                };
                break;
            case 'MONTHLY_BILL':
            case 'PAYMENT_FROM_ADVANCE':
            case 'PAYMENT_AFTER_ADVANCE':
                return match ($customer->type) {
                    'PPP_FREE', 'PPP_DAILY', 'HOTSPOT_FREE', 'HOTPOST_DAILY', 'HOTPOST_MONTHLY', 'STATIC_FREE', 'STATIC_DAILY', 'OTHER_FREE', 'OTHER_DAILY' => match ($customer->overall_status) {
                        default => false
                    },
                    'PPP_MONTHLY', 'STATIC_MONTHLY', 'OTHER_MONTHLY' => match ($customer->overall_status) {
                        default => true
                    },
                };
                break;
            case 'OTHER_PAYMENT':
                return true;
                break;
            case 'BILLING_PROFILE_CHANGE':
                return match ($customer->type) {
                    'PPP_FREE', 'HOTSPOT_FREE', 'HOTPOST_DAILY', 'HOTPOST_MONTHLY', 'STATIC_FREE', 'STATIC_DAILY', 'OTHER_FREE', 'OTHER_DAILY'  => match ($customer->overall_status) {
                        default => false
                    },
                    'PPP_DAILY', 'PPP_MONTHLY', 'STATIC_MONTHLY', 'OTHER_MONTHLY' => match ($customer->overall_status) {
                        default => true
                    },
                };
                break;
        }

        return false;
    }
}
