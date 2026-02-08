<?php

namespace App\Http\Controllers;

use App\Jobs\BroadcastPaymentLinks;
use App\Models\billing_profile;
use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use Illuminate\Http\Request;

class PaymentLinkBroadcastController extends Controller
{


    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $operator = $request->user();

        $where = [
            ['operator_id', '=', $operator->id],
            ['payment_status', '=', 'billed'],
        ];

        $customers_count = customer::where($where)->count();

        $bill = customer_bill::where('operator_id', $operator->id)->firstOr(function () {
            return
                customer_bill::make([
                    'id' => 0,
                    'customer_id' => 0,
                ]);
        });

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.payment-link-broadcast', [
                    'customers_count' => $customers_count,
                    'bill' => $bill,
                ]);
                break;

            case 'operator':
                return view('admins.operator.payment-link-broadcast', [
                    'customers_count' => $customers_count,
                    'bill' => $bill,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.payment-link-broadcast', [
                    'customers_count' => $customers_count,
                    'bill' => $bill,
                ]);
                break;
        }
    }

    /**
     * Show customer count
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Models\billing_profile $billing_profile
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, billing_profile $billing_profile)
    {
        $operator = $request->user();

        $where = [
            ['operator_id', '=', $operator->id],
            ['payment_status', '=', 'billed'],
            ['billing_profile_id', '=', $billing_profile->id],
        ];

        return customer::where($where)->count();
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
            'text_message' => 'required|string',
            'billing_profile_id' => 'nullable|numeric',
        ]);

        $operator = $request->user();

        $connection = config('app.env') == 'production' ? 'redis' : 'database';
        BroadcastPaymentLinks::dispatch($operator->id, $request->text_message, $request->billing_profile_id)
            ->onConnection($connection)
            ->onQueue('default');

        return redirect()->route('sms_histories.index')->with('success', 'Messages will be sent sortly!');
    }
}
