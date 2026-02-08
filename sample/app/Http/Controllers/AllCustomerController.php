<?php

namespace App\Http\Controllers;

use App\Models\all_customer;
use App\Models\Freeradius\customer;
use Illuminate\Support\Facades\Log;

class AllCustomerController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\Freeradius\customer $customer
     * @return void
     */
    public static function updateOrCreate(customer $customer)
    {
        // validate
        $country_code = getCountryCode($customer->operator_id);
        $mobile = validate_mobile($customer->mobile, $country_code);
        if ($mobile == 0) {
            $mobile = null;
        }

        $mobile_e164 = getE164PhoneNumber($customer->mobile, $country_code);
        if ($mobile_e164 == 0) {
            $mobile_e164 = null;
        }

        try {
            // Store or Update
            all_customer::updateOrCreate(
                [
                    'mgid' => $customer->mgid,
                    'customer_id' => $customer->id,
                ],
                [
                    'operator_id' => $customer->operator_id,
                    'mobile' => $mobile,
                    'mobile_e164' => $mobile_e164,
                ]
            );
        } catch (\Throwable $th) {
            Log::channel('validate_mobile')->debug('all_customer updateOrCreate Error : ' . $th);
            return 0;
        }

        return 1;
    }
}
