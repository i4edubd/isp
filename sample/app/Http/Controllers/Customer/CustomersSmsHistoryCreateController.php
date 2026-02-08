<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Sms\SmsGatewayController;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Http\Request;

class CustomersSmsHistoryCreateController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, customer $customer)
    {
        $operator = $request->user();

        switch ($operator->role) {

            case 'group_admin':
                return view('admins.group_admin.customers-sms-history-create', [
                    'customer' => $customer,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customers-sms-history-create', [
                    'customer'  => $customer,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customers-sms-history-create', [
                    'customer'  => $customer,
                ]);
                break;

            case 'manager':
                return view('admins.manager.customers-sms-history-create', [
                    'customer'  => $customer,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, customer $customer)
    {
        $request->validate([
            'message' => 'required',
        ]);

        $mobile = validate_mobile($customer->mobile, getCountryCode($request->user()->id));
        if (!$mobile) {
            return redirect()->route('customers.index')->with('info', 'Invalid Mobile Number!');
        }

        $controller = new SmsGatewayController();

        $operator = operator::find($customer->operator_id);

        $controller->sendSms($operator, $mobile, $request->message, $customer->id);

        return redirect()->route('customers.index')->with('success', 'SMS has been sent');
    }
}
