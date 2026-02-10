<?php

namespace App\Http\Controllers;

use App\Models\account;
use App\Models\operators_online_payment;
use App\Models\payment_gateway;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DebitAccountOnlineRechageController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\account  $account
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, account $account)
    {
        $this->authorize('onlineRechage', $account);

        $payment_gateways = payment_gateway::where('operator_id', $account->account_provider)->get();
        $payment_gateways = $payment_gateways->filter(function ($value, $key) {
            switch ($value->provider_name) {
                case 'send_money':
                    return false;
                    break;
                case 'bkash_payment':
                    return false;
                    break;
                default:
                    return true;
                    break;
            }
        });

        $role = $request->user()->role;

        switch ($role) {
            case 'operator':
                return view('admins.operator.accounts-onlineRechage', [
                    'account' => $account,
                    'payment_gateways' => $payment_gateways,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.accounts-onlineRechage', [
                    'account' => $account,
                    'payment_gateways' => $payment_gateways,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\account  $account
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, account $account)
    {
        $this->authorize('onlineRechage', $account);

        $request->validate([
            'payment_gateway_id' => 'required|numeric',
            'amount' => 'required|numeric',
        ]);

        $payment_gateway = payment_gateway::findOrFail($request->payment_gateway_id);

        $operators_online_payment = new operators_online_payment();
        $operators_online_payment->operator_id = $request->user()->id;
        $operators_online_payment->account_id  = $account->id;
        $operators_online_payment->payment_gateway_id = $request->payment_gateway_id;
        $operators_online_payment->payment_gateway_name = $payment_gateway->provider_name;
        $operators_online_payment->payment_purpose = 'cash_in';
        $operators_online_payment->currency = getCurrency($request->user()->id);
        $operators_online_payment->amount_paid = $request->amount;
        $operators_online_payment->mer_txnid = Carbon::now()->timestamp;
        $operators_online_payment->date = date(config('app.date_format'));
        $operators_online_payment->month = date(config('app.month_format'));
        $operators_online_payment->year = date(config('app.year_format'));
        $operators_online_payment->save();

        return OperatorsOnlinePaymentController::create($operators_online_payment);
    }
}
