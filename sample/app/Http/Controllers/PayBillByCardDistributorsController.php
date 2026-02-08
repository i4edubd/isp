<?php

namespace App\Http\Controllers;

use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use App\Models\package;
use App\Models\recharge_card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PayBillByCardDistributorsController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $customer_id
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, int $customer_id)
    {
        $card_distributor = $request->user('card');
        $bill = customer_bill::where('operator_id', $card_distributor->operator_id)->where('customer_id', $customer_id)->first();
        if (!$bill) {
            return redirect()->route('card.search-customer.create')->with('info', 'Bill not found.');
        }
        $package_count = package::where('id', $bill->package_id)->where('operator_id', $card_distributor->operator_id)->where('name', '!=', 'Trial')->where('price', $bill->amount)->count();
        if ($package_count == 0) {
            return redirect()->route('card.search-customer.create')->with('info', 'Package of bill amount not found.');
        }

        $operator = CacheController::getOperator($card_distributor->operator_id);
        $model = new customer();
        $model->setConnection($operator->node_connection);
        $customer = $model->findOrFail($customer_id);
        $currency = getCurrency($operator->id);

        return view('admins.card_distributors.pay-bill', [
            'customer' => $customer,
            'bill_amount' => $bill->amount,
            'currency' => $currency,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $customer_id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, int $customer_id)
    {
        $card_distributor = $request->user('card');
        $operator = CacheController::getOperator($card_distributor->operator_id);
        $bill = customer_bill::where('operator_id', $card_distributor->operator_id)->where('customer_id', $customer_id)->firstOrFail();
        $package = package::where('id', $bill->package_id)->where('operator_id', $card_distributor->operator_id)->where('name', '!=', 'Trial')->where('price', $bill->amount)->firstOrFail();

        $recharge_card = recharge_card::where('operator_id', $operator->id)
            ->where('card_distributor_id', $card_distributor->id)
            ->where('package_id', $package->id)
            ->where('status', 'unused')
            ->where('locked', '!=', 1)
            ->first();

        if (!$recharge_card) {
            return redirect()->route('card.search-customer.create')->with('error', 'Recharge Card Not Found');
        }

        $model = new customer();
        $model->setConnection($operator->node_connection);
        $customer = $model->findOrFail($customer_id);

        Gate::forUser($customer)->authorize('useRechargeCard', [$recharge_card]);
        $recharge_card->locked = 1;
        $recharge_card->save();

        RechargeCardRechargeController::payBill($customer, $recharge_card, $bill);

        return redirect()->route('card.search-customer.show', ['customer_id' => $customer->id])->with('info', 'Payment successful');
    }
}
