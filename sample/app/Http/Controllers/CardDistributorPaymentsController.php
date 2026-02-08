<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Sms\SmsGatewayController;
use App\Models\card_distributor;
use App\Models\card_distributor_payments;
use Illuminate\Http\Request;

class CardDistributorPaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // default length
        $length = 10;

        if ($request->filled('length')) {
            $length = $request->length;
        }

        // filter
        $where = [
            ['operator_id', '=', $request->user()->id],
        ];

        if ($request->filled('distributor_id')) {
            $where[] = ['card_distributor_id', '=', $request->distributor_id];
        }

        $distributors = card_distributor::where('operator_id', $request->user()->id)->get();

        $payments = card_distributor_payments::where($where)->orderBy('id', 'desc')->paginate($length);

        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.distributor-payments', [
                    'distributors' => $distributors,
                    'payments' => $payments,
                    'length' => $length,
                ]);
                break;

            case 'operator':
                return view('admins.operator.distributor-payments', [
                    'distributors' => $distributors,
                    'payments' => $payments,
                    'length' => $length,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.distributor-payments', [
                    'distributors' => $distributors,
                    'payments' => $payments,
                    'length' => $length,
                ]);
                break;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $distributors = card_distributor::where('operator_id', $request->user()->id)->get();

        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.distributor-payments-create', [
                    'distributors' => $distributors,
                ]);
                break;

            case 'operator':
                return view('admins.operator.distributor-payments-create', [
                    'distributors' => $distributors,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.distributor-payments-create', [
                    'distributors' => $distributors,
                ]);
                break;
        }
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
            'card_distributor_id' => 'required|numeric',
            'amount_paid' => 'required|numeric',
        ]);


        $card_distributor = card_distributor::findOrFail($request->card_distributor_id);
        $card_distributor->amount_due = $card_distributor->amount_due - $request->amount_paid;
        $card_distributor->save();

        $card_distributor_payment = new card_distributor_payments();
        $card_distributor_payment->operator_id = $request->user()->id;
        $card_distributor_payment->card_distributor_id = $request->card_distributor_id;
        $card_distributor_payment->amount_paid = $request->amount_paid;
        $card_distributor_payment->date = date(config('app.date_format'));
        $card_distributor_payment->week = date(config('app.week_format'));
        $card_distributor_payment->month = date(config('app.month_format'));
        $card_distributor_payment->year = date(config('app.year_format'));
        $card_distributor_payment->save();

        $message = SmsGenerator::paymentConfirmationMsg($request->user(), $request->amount_paid);

        $controller = new SmsGatewayController();

        $controller->sendSms($request->user(), $card_distributor->mobile, $message);

        return redirect()->route('distributor_payments.index')->with('success', 'Payment Received with thanks!');
    }
}
