<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customer\PPPoECustomersFramedIPAddressController;
use App\Http\Controllers\Customer\StaticIpCustomersFirewallController;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Http\Request;

class OperatorActivateController extends Controller
{


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, operator $operator)
    {
        if ($request->user()->id !== $operator->gid) {
            abort(403);
        }

        $operator->status = 'active';
        $operator->save();

        $where = [
            ['mgid', '=', $operator->mgid],
            ['operator_id', '=', $operator->id],
            ['status', '=', 'suspended'],
            ['suspend_reason', '=', 'group_admin'],
        ];

        $customers = customer::where($where)->get();

        foreach ($customers as $customer) {

            $customer->status = 'active';
            $customer->suspend_reason = 'payment_due';
            $customer->save();

            switch ($customer->connection_type) {
                case 'PPPoE':
                    PPPoECustomersFramedIPAddressController::updateOrCreate($customer);
                    PPPCustomerDisconnectController::disconnect($customer);
                    break;
                case 'StaticIp':
                    StaticIpCustomersFirewallController::updateOrCreate($customer);
                    break;
            }
        }

        return redirect()->route('operators.index')->with('success', 'Operator has been activated successfully');
    }
}
