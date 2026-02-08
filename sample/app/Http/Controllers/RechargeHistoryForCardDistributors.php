<?php

namespace App\Http\Controllers;

use App\Models\recharge_card;
use Illuminate\Http\Request;

class RechargeHistoryForCardDistributors extends Controller
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

        if ($request->filled('pin')) {
            $recharge_cards = recharge_card::with('package')
                ->where('card_distributor_id', $card_distributor->id)
                ->where('status', 'used')
                ->where('pin', $request->pin)
                ->select('id', 'mobile', 'pin', 'card_distributor_id', 'package_id', 'status', 'commission', 'updated_at')
                ->orderByDesc('updated_at')
                ->limit(100)
                ->get();

            return view('admins.card_distributors.recharge-history', [
                'recharge_cards' => $recharge_cards,
            ]);
        }

        $recharge_cards = recharge_card::with('package')
            ->where('card_distributor_id', $card_distributor->id)
            ->where('status', 'used')
            ->select('id', 'mobile', 'pin', 'card_distributor_id', 'package_id', 'status', 'commission', 'updated_at')
            ->orderByDesc('updated_at')
            ->limit(100)
            ->get();

        return view('admins.card_distributors.recharge-history', [
            'recharge_cards' => $recharge_cards,
        ]);
    }
}
