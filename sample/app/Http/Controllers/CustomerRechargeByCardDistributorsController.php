<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use App\Models\package;
use App\Models\recharge_card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CustomerRechargeByCardDistributorsController extends Controller
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
        $operator = CacheController::getOperator($card_distributor->operator_id);
        $model = new customer();
        $model->setConnection($operator->node_connection);
        $customer = $model->findOrFail($customer_id);

        $packages = package::with('master_package')->where('operator_id', $operator->id)->where('name', '!=', 'Trial')->get();
        $packages = $packages->filter(function ($package, $key) use ($customer) {
            return $package->master_package->connection_type == $customer->connection_type && recharge_card::where('package_id', $package->id)->count();
        });
        $currency = getCurrency($operator->id);

        return view('admins.card_distributors.recharge', [
            'customer' => $customer,
            'packages' => $packages,
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

        $request->validate([
            'package_id' => 'required|numeric',
        ]);

        $package = package::findOrFail($request->package_id);
        $card_distributor = $request->user('card');
        $operator = CacheController::getOperator($card_distributor->operator_id);

        $recharge_card = recharge_card::where('operator_id', $operator->id)
            ->where('card_distributor_id', $card_distributor->id)
            ->where('package_id', $package->id)
            ->where('status', 'unused')
            ->where('locked', '!=', 1)
            ->first();

        if (!$recharge_card) {
            return redirect()->route('card.customer.recharge.create', ['customer_id' => $customer_id])->with('error', 'Recharge Card Not Found');
        }

        $model = new customer();
        $model->setConnection($operator->node_connection);
        $customer = $model->findOrFail($customer_id);

        Gate::forUser($customer)->authorize('useRechargeCard', [$recharge_card]);
        $recharge_card->locked = 1;
        $recharge_card->save();

        RechargeCardRechargeController::recharge($customer, $recharge_card);

        return redirect()->route('card.search-customer.show', ['customer_id' => $customer->id])->with('info', 'Recharge successful');
    }
}
