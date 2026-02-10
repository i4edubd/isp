<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Sms\SmsGatewayController;
use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Http\Request;

class SendPaymentLinkController extends Controller
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

        $operator_id = $operator->id;

        if ($operator->role == 'manager') {
            $operator_id = $operator->group_admin->id;
        }

        $bill = customer_bill::where('operator_id', $operator_id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$bill) {
            return redirect()->route('customers.index')->with('error', 'No Bill Found!');
        }

        switch ($operator->role) {

            case 'group_admin':
                return view('admins.group_admin.send-payment-link', [
                    'customer' => $customer,
                    'bill' => $bill,
                ]);
                break;

            case 'operator':
                return view('admins.operator.send-payment-link', [
                    'customer'  => $customer,
                    'bill' => $bill,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.send-payment-link', [
                    'customer'  => $customer,
                    'bill' => $bill,
                ]);
                break;

            case 'manager':
                return view('admins.manager.send-payment-link', [
                    'customer'  => $customer,
                    'bill' => $bill,
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
        $this->authorize('sendLink', $customer);

        $request->validate([
            'message' => 'required|string',
        ]);

        $mobile = $customer->mobile;

        $controller = new SmsGatewayController();

        $operator = operator::find($customer->operator_id);

        $controller->sendSms($operator, $mobile, $request->message, $customer->id);

        return redirect()->route('customers.index')->with('success', 'SMS has been sent');
    }
}
