<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Sms\SmsGatewayController;
use App\Http\Controllers\SmsGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerMobileVerificationController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $customer = $request->user('customer');
        $operator = CacheController::getOperator($customer->operator_id);

        //send and save otp
        if ($request->session()->has('customer_mobile_verification_sms_sent') == false) {
            $otp = random_int(1000, 9999);
            $message = SmsGenerator::OTP($operator, $otp);
            try {
                SmsGatewayController::sendSms($operator, $customer->mobile, $message, 0);
            } catch (\Throwable $th) {
                Log::channel('stack')->error('@customer.mobile.verification => ' . $th);
            }
            session(['customer_mobile_verification_sms_sent' => $otp]);
        }

        return view('customers.mobile-verification-form', [
            'operator' => $operator,
            'customer' => $customer,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'otp' => 'required',
        ]);

        $otp = $request->session()->get('customer_mobile_verification_sms_sent', 0);
        if ($otp != $request->otp) {
            return redirect()->route('customer.mobile.verification')->with('error', 'Invalid PIN');
        }

        $customer = $request->user('customer');
        $customer->verified_mobile = 1;
        $customer->save();

        $request->session()->forget('customer_mobile_verification_sms_sent');

        return redirect()->route('customers.home')->with('success', 'Mobile Number has been verified successfully!');
    }
}
