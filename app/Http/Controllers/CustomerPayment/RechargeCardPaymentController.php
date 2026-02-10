<?php

namespace App\Http\Controllers\CustomerPayment;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RechargeCardRechargeController;
use App\Models\customer_payment;
use App\Models\package;
use App\Models\recharge_card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class RechargeCardPaymentController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param \App\Models\customer_payment $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function create(customer_payment $customer_payment)
    {
        $operator = CacheController::getOperator($customer_payment->operator_id);
        $package = package::find($customer_payment->package_id);
        return view('customers.recharge-card-use', [
            'operator' => $operator,
            'customer_payment' => $customer_payment,
            'package' => $package,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Models\customer_payment $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, customer_payment $customer_payment)
    {
        $request->validate([
            'pin' => 'required|numeric',
        ]);

        $pin_where = [
            ['operator_id', '=', $customer_payment->operator_id],
            ['package_id', '=', $customer_payment->package_id],
            ['status', '=', 'unused'],
            ['pin', '=', $request->pin],
            ['locked', '=', 0],
        ];

        $card_count = recharge_card::where($pin_where)->count();

        if ($card_count == 0) {
            return redirect()->route('customers.packages')->with('error', 'Invalid PIN code or card used.');
        }

        $all_customer = $request->user('customer');
        $customer = CacheController::getCustomerUseAllCustomer($all_customer);

        // Lock card
        $card = DB::transaction(function () use ($pin_where, $customer) {
            $card = recharge_card::where($pin_where)->lockForUpdate()->firstOrFail();
            Gate::forUser($customer)->authorize('useRechargeCard', [$card]);
            $card->locked = 1;
            $card->save();
            return $card;
        });

        RechargeCardRechargeController::makePayment($card, $customer_payment);

        //Show Profile
        return redirect()->route('customers.profile')->with('success', 'Package has been activated successfully');
    }
}
