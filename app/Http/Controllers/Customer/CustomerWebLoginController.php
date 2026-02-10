<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Models\all_customer;
use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerWebLoginController extends Controller
{

    /**
     * Process Customer Login Request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // <<process payment request>>
        $request->validate([
            'cid' => 'nullable|numeric',
            'bid' => 'nullable|numeric',
        ]);

        $payment_request = 1;

        $bill_where = [];

        if ($request->filled('cid')) {
            $bill_where[] = ['customer_id', '=', $request->cid];
        } else {
            $payment_request = 0;
        }

        if ($request->filled('bid')) {
            $bill_where[] = ['id', '=', $request->bid];
        } else {
            $payment_request = 0;
        }

        if ($payment_request) {
            if (customer_bill::where($bill_where)->count()) {
                $bill = customer_bill::where($bill_where)->firstOr(function () {
                    abort(404, 'bill not found');
                });
                $operator = operator::where('id', $bill->operator_id)->firstOr(function () {
                    abort(404, 'operator not found');
                });
                $all_customer = all_customer::where('operator_id', $operator->id)->where('customer_id', $bill->customer_id)->firstOr(function () {
                    abort(404, 'customer in all_customers not found');
                });
                self::startWebSession($all_customer);
                $request->session()->regenerate();
                return redirect()->route('customers.bills');
            }
        }

        return view('customers.web-login');
    }

    /**
     * Process Customer Login Request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required|numeric',
        ]);

        $mobile = validate_mobile($request->mobile);
        if ($mobile == 0) {
            abort(500, 'Invalid Mobile Number');
        }

        $all_customer = all_customer::where('mobile', $mobile)->firstOrFail();

        if ($request->filled('customer_id')) {
            $operator = CacheController::getOperator($all_customer->operator_id);
            $model = new customer();
            $model->setConnection($operator->node_connection);
            $model->where('mgid', $all_customer->mgid)->where('id', $request->customer_id)->firstOrFail();
        }

        Auth::guard('customer')->login($all_customer, true);
        $request->session()->regenerate();

        return redirect()->route('customers.home');
    }

    /**
     * Process Customer Logout Request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('root');
    }

    /**
     * Start Web Session
     *
     * @param  \App\Models\all_customer
     */
    public static function startWebSession(all_customer $customer)
    {
        Auth::guard('customer')->login($customer, true);
    }
}
