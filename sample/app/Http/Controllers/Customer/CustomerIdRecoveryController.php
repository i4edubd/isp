<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Sms\SmsGatewayController;
use App\Http\Controllers\SmsGenerator;
use App\Models\all_customer;
use App\Models\operator;
use Illuminate\Http\Request;

class CustomerIdRecoveryController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customers.forgot-customer-id');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validation
        $request->validate([
            'mobile' => 'required',
        ]);

        $customer_mobile = validate_mobile($request->mobile);

        if ($customer_mobile == 0) {
            abort(500, 'Invalid Mobile Number');
        }

        // fetch customer
        $customer = all_customer::where('mobile', $customer_mobile)->firstOrFail();

        //send Customer ID
        if ($request->session()->has('customer_id_sent') == false) {

            $operator = operator::findOrFail($customer->operator_id);

            $message = SmsGenerator::customerId($operator, $customer->customer_id);

            $sms_gateway = new SmsGatewayController();

            $sms_gateway->sendSms($operator, $customer->mobile, $message, $customer->customer_id);

            session(['customer_id_sent' => $customer->customer_id]);
        }

        return redirect()->route('root')->with('success', 'Customer ID has been sent to your mobile number');
    }
}
