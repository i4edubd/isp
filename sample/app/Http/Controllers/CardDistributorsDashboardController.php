<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CardDistributorsDashboardController extends Controller
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
        $balance = $card_distributor->account_balance;

        return view('admins.card_distributors.dashboard', [
            'balance' => $balance,
        ]);
    }
}
