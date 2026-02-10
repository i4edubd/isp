<?php

namespace App\Http\Controllers;

use App\Models\card_distributor;
use App\Models\yearly_card_distributor_payment;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class YearlyCardDistributorPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $payments = yearly_card_distributor_payment::where('operator_id', $operator->id)->get();

        if ($request->filled('distributor_id')) {
            $distributor_id = $request->distributor_id;
            $payments = $payments->filter(function ($payment) use ($distributor_id) {
                return $payment->card_distributor_id == $distributor_id;
            });
        }

        if ($request->filled('year')) {
            $year = $request->year;
            $payments = $payments->filter(function ($payment) use ($year) {
                return $payment->year == $year;
            });
        }

        $distributors = card_distributor::where('operator_id', $request->user()->id)->get();

        $length = 50;

        if ($request->filled('length')) {
            $length = $request->length;
        }

        $current_page = $request->input("page") ?? 1;

        $view_payments = new LengthAwarePaginator($payments->forPage($current_page, $length), $payments->count(), $length, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.yearly_card_distributor_payments', [
                    'distributors' => $distributors,
                    'payments' => $view_payments,
                    'length' => $length,
                ]);
                break;

            case 'operator':
                return view('admins.operator.yearly_card_distributor_payments', [
                    'distributors' => $distributors,
                    'payments' => $view_payments,
                    'length' => $length,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.yearly_card_distributor_payments', [
                    'distributors' => $distributors,
                    'payments' => $view_payments,
                    'length' => $length,
                ]);
                break;
        }
    }
}
