<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Sms\SmsGatewayController;
use App\Http\Controllers\SmsGenerator;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Http\Request;

class SmsMessagesForCustomerController extends Controller
{


    /**
     * Send package purchase package.
     *
     * @param App\Models\Freeradius\customer
     * @return int
     */

    public static function purchasePackage(customer $customer)
    {

        $operator = operator::find($customer->operator_id);

        $message = SmsGenerator::noBalanceNotice($operator);

        $sms_gateway = new SmsGatewayController();

        $sms_gateway->sendSms($operator, $customer->mobile, $message, $customer->id);

        return 0;
    }


    /**
     * Send welcome message for hotspot customers.
     *
     * @param App\Models\Freeradius\customer
     * @return int
     */
    public static function hotspotRegistration(customer $customer)
    {
        $operator = operator::find($customer->operator_id);

        $message = SmsGenerator::welcomeMsgHotspot($operator);

        $sms_gateway = new SmsGatewayController();

        $sms_gateway->sendSms($operator, $customer->mobile, $message, $customer->id);

        return 0;
    }


    /**
     * Send welcome message for ppp customers.
     *
     * @param App\Models\Freeradius\customer
     * @return int
     */
    public static function pppRegistration(customer $customer)
    {
        $operator = operator::find($customer->operator_id);

        $message = SmsGenerator::welcomeMsgPpp($operator, $customer);

        $sms_gateway = new SmsGatewayController();

        $sms_gateway->sendSms($operator, $customer->mobile, $message, $customer->id);

        return 0;
    }
}
