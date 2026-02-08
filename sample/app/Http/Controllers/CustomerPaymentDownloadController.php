<?php

namespace App\Http\Controllers;

use App\Models\customer_payment;
use App\Models\operator;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;

class CustomerPaymentDownloadController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                $where = [
                    ['gid', '=', $operator->id],
                    ['role', '!=', 'manager'],
                ];
                $operators = operator::where($where)->get();
                return view('admins.group_admin.customer-payments-download-create', [
                    'operators' => $operators,
                ]);
                break;

            case 'operator':
                $operators = 0;
                return view('admins.operator.customer-payments-download-create', [
                    'operators' => $operators,
                ]);
                break;

            case 'sub_operator':
                $operators = 0;
                return view('admins.sub_operator.customer-payments-download-create', [
                    'operators' => $operators,
                ]);
                break;

            case 'super_admin':
                $where = [
                    ['sid', '=', $operator->id],
                    ['role', '=', 'group_admin'],
                ];
                $operators = operator::where($where)->get();
                return view('admins.super_admin.customer-payments-download-create', [
                    'operators' => $operators,
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
        // validate
        $request->validate([
            'type' => 'in:Cash,Online,RechargeCard|nullable',
        ]);

        $filter = [];

        // pay_status || Default filter
        $filter[] = ['pay_status', '=', 'Successful'];

        // operator_id
        if ($request->filled('operator_id')) {
            $filter[] = ['operator_id', '=', $request->operator_id];
        } else {
            $filter[] = ['operator_id', '=', $request->user()->id];
        }

        // type
        if ($request->user()->role == 'super_admin') {
            $filter[] = ['type', '=', 'Online'];
        } else {
            if ($request->filled('type')) {
                $filter[] = ['type', '=', $request->type];
            }
        }

        // year
        if ($request->filled('year')) {
            $filter[] = ['year', '=', $request->year];
        }

        // month
        if ($request->filled('month')) {
            $filter[] = ['month', '=', $request->month];
        }

        // cash_collector_id
        if ($request->filled('cash_collector_id')) {
            $filter[] = ['cash_collector_id', '=', $request->cash_collector_id];
        }

        // date
        if ($request->filled('date')) {
            $filter[] = ['date', '=', date_format(date_create($request->date), config('app.date_format'))];
        }

        $payments = customer_payment::where($filter)->get();

        // note
        if ($request->filled('note')) {
            $note = $request->note;
            $payments = $payments->filter(function ($payment) use ($note) {
                return false !== stristr($payment->note, $note);
            });
        }

        if (count($payments) == 0) {
            return redirect()->route('customer_payments.index')->with('error', 'Nothing to Download');
        }

        $writer = SimpleExcelWriter::streamDownload('customer-payments.xlsx');

        $total_paid = 0;
        $total_fee = 0;
        $total_store = 0;

        foreach ($payments as $payment) {

            $total_paid = $total_paid + $payment->amount_paid;

            $total_fee = $total_fee + $payment->transaction_fee;

            $total_store = $total_store + $payment->store_amount;

            $writer->addRow([
                'Admin ID' => $payment->gid,
                'Operator ID' => $payment->operator_id,
                'Cash Collector id' => $payment->cash_collector_id,
                'customer ID' => $payment->customer_id,
                'Mobile' => $payment->mobile,
                'Username' => $payment->username,
                'Payment Gateway' => $payment->payment_gateway_name,
                'Card Distributor' => $payment->recharge_card->distributor->name,
                'Type' => $payment->type,
                'status' => $payment->pay_status,
                'Amount Paid' => $payment->amount_paid,
                'Transaction Fee' => $payment->transaction_fee,
                'Discount' => $payment->discount,
                'Store Amount' => $payment->store_amount,
                'VAT' => $payment->vat_paid,
                'First Party' => $payment->first_party,
                'Second Party' => $payment->second_party,
                'Third Party' => $payment->third_party,
                'Mer TrxnID' => $payment->mer_txnid,
                'PGW TrxnID' => $payment->pgw_txnid,
                'year' => $payment->year,
                'month' => $payment->month,
                'date' => $payment->date,
                'note' => $payment->note,
            ]);
        }

        $writer->addRow([
            'Admin ID' => "",
            'Operator ID' => "",
            'Cash Collector id' => "",
            'customer ID' => "",
            'Mobile' => "",
            'Username' => "",
            'Payment Gateway' => "",
            'Card Distributor' => "",
            'Type' => "",
            'status' => "Total",
            'Amount Paid' => $total_paid,
            'Transaction Fee' => $total_fee,
            "Discount" => "",
            'Store Amount' => $total_store,
            'VAT' => "",
            'First Party' => "",
            'Second Party' => "",
            'Third Party' => "",
            'Mer TrxnID' => "",
            'PGW TrxnID' => "",
            'year' => "",
            'month' => "",
            'date' => "",
            'note' => "",
        ]);

        $writer->toBrowser();
    }
}
