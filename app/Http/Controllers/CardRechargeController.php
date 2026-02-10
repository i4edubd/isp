<?php

namespace App\Http\Controllers;

use App\Models\recharge_card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CardRechargeController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $all_customer = $request->user('customer');
        $operator = CacheController::getOperator($all_customer->operator_id);
        return view('customers.card-recharge', [
            'operator' => $operator,
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
            'card_pin' => 'required|string|max:32',
        ]);

        $all_customer = $request->user('customer');
        $customer = CacheController::getCustomerUseAllCustomer($all_customer);
        $operator = CacheController::getOperator($customer->operator_id);
        $card_pin = $request->card_pin;

        $pin_where = [
            ['operator_id', '=', $operator->id],
            ['status', '=', 'unused'],
            ['pin', '=', $card_pin],
            ['locked', '=', 0],
        ];
        $card_count = recharge_card::where($pin_where)->count();

        if ($card_count == 0) {
            return redirect()->route('customers.packages')->with('error', 'Invalid PIN code or card used.');
        }

        if ($card_count > 1) {
            return redirect()->route('customers.packages')->with('error', 'Processing Error! Try using Buy Package menu');
        }

        // Lock card
        $recharge_card = DB::transaction(function () use ($pin_where, $customer) {
            $card = recharge_card::where($pin_where)->lockForUpdate()->firstOrFail();
            Gate::forUser($customer)->authorize('useRechargeCard', [$card]);
            $card->locked = 1;
            $card->save();
            return $card;
        });

        //process payment
        RechargeCardRechargeController::recharge($customer, $recharge_card);

        //Show Profile
        return redirect()->route('customers.profile')->with('success', 'Package has been activated successfully');
    }
}
