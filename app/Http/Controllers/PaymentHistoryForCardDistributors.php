<?php

namespace App\Http\Controllers;

use App\Models\card_distributor_payments;
use Illuminate\Http\Request;

class PaymentHistoryForCardDistributors extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $card_distributor = $request->user('card');

        $card_distributor_payments = card_distributor_payments::where('card_distributor_id', $card_distributor->id)
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return view('admins.card_distributors.payment-history', [
            'card_distributor_payments' => $card_distributor_payments,
        ]);
    }
}
