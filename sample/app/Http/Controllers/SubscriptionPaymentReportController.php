<?php

namespace App\Http\Controllers;

use App\Models\subscription_payment;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;

class SubscriptionPaymentReportController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admins.super_admin.subscription-payment-report-create');
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
            'year' => 'required|numeric',
            'month' => 'required|string',

        ]);

        $where = [
            ['year', '=', $request->year],
            ['month', '=', $request->month],
            ['pay_status', '=', 'Successful'],
        ];

        $payments = subscription_payment::where($where)->get();

        $file = 'subscription-payments-report-' . $request->month . '-' . $request->year . '.xlsx';

        $writer = SimpleExcelWriter::streamDownload($file);

        $total_fee = 0;

        $total_super_admin = 0;

        $total_developer = 0;

        foreach ($payments as $payment) {

            $developers_share = config('consumer.developers_share');

            $developers_amount = round(($developers_share / 100) * $payment->store_amount);

            $super_admins_share = $payment->store_amount - $developers_amount;

            $writer->addRow([
                'Date' => $payment->date,
                'Admin ID' => $payment->mgid,
                'Amount Paid' => $payment->amount_paid,
                'PGW Txnid' => $payment->pgw_txnid,
                'Bank Txnid' => $payment->bank_txnid,
                'Txn Fee' => $payment->transaction_fee,
                '%super_admin' => $super_admins_share,
                '%developer' => $developers_amount,
            ]);

            $total_fee = $total_fee + $payment->transaction_fee;

            $total_super_admin = $total_super_admin + $super_admins_share;

            $total_developer = $total_developer + $developers_amount;
        }

        $writer->addRow([
            'Date' => "",
            'Admin ID' => "",
            'Amount Paid' => "",
            'PGW Txnid' => "",
            'Bank Txnid' => "Total",
            'Txn Fee' => $total_fee,
            '%super_admin' => $total_super_admin,
            '%developer' => $total_developer,
        ]);

        $writer->toBrowser();
    }
}
