<?php

namespace App\Http\Controllers;

use App\Models\account;
use App\Models\cash_in;
use App\Models\cash_out;
use App\Models\operators_online_payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OperatorsOnlinePaymentController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\operators_online_payment  $operators_online_payment
     * @return \Illuminate\Http\Response
     */
    public static function create(operators_online_payment $operators_online_payment)
    {
        switch ($operators_online_payment->payment_gateway_name) {
            case 'bkash_tokenized_checkout':
                return redirect()->route('bkash_tokenized_checkout.operators_online_payment.create', ['operators_online_payment' => $operators_online_payment]);
            case 'bkash_checkout':
                return redirect()->route('bkash.operators_online_payment.initiate', ['operators_online_payment' => $operators_online_payment]);
                break;
            case 'nagad':
                return redirect()->route('nagad.operators_online_payment.initiate', ['operators_online_payment' => $operators_online_payment]);
                break;
            case 'shurjopay':
                return redirect()->route('ShurjoPay.operators_online_payment.create', ['operators_online_payment' => $operators_online_payment]);
                break;
            case 'sslcommerz':
                return redirect()->route('sslcommerz.operators_online_payment.create', ['operators_online_payment' => $operators_online_payment]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\operators_online_payment  $operators_online_payment
     * @return \Illuminate\Http\Response
     */
    public static function store(operators_online_payment $operators_online_payment)
    {
        if ($operators_online_payment->used !== 0) {
            return 0;
        }

        if ($operators_online_payment->pay_status !== 'Successful') {
            return 0;
        }

        $operator = CacheController::getOperator($operators_online_payment->operator_id);

        $account = account::findOrFail($operators_online_payment->account_id);

        if ($operators_online_payment->payment_purpose == 'cash_in') {
            $cash_in = new cash_in();
            $cash_in->account_id = $operators_online_payment->account_id;
            $cash_in->transaction_code = 5;
            $cash_in->transaction_id = $operators_online_payment->id;
            $cash_in->name = $operator->name;
            $cash_in->username = $operator->email;
            $cash_in->amount = $operators_online_payment->store_amount;
            $cash_in->date = date(config('app.date_format'));
            $cash_in->old_balance = $account->balance;
            $cash_in->new_balance = $account->balance + $cash_in->amount;
            $cash_in->month = date(config('app.month_format'));
            $cash_in->year = date(config('app.year_format'));
            $cash_in->save();

            // update account balance
            $amount = $cash_in->amount;
            DB::transaction(function () use ($account, $amount) {
                $the_account = account::lockForUpdate()->find($account->id);
                $the_account->balance = $the_account->balance + $amount;
                $the_account->save();
            });

            return redirect()->route('accounts.receivable')->with('success', 'Payment successful');
        }

        if ($operators_online_payment->payment_purpose == 'cash_out') {

            $cash_out = new cash_out();
            $cash_out->account_id = $account->id;
            $cash_out->transaction_code = 7;
            $cash_out->transaction_id = $operators_online_payment->id;
            $cash_out->name = $operator->name;
            $cash_out->username = $operator->email;
            $cash_out->amount = $operators_online_payment->store_amount;
            $cash_out->date = date(config('app.date_format'));
            $cash_out->old_balance = $account->balance;
            $cash_out->new_balance = $account->balance - $cash_out->amount;
            $cash_out->month = date(config('app.month_format'));
            $cash_out->year = date(config('app.year_format'));
            $cash_out->save();

            // update balance
            DB::transaction(function () use ($account, $cash_out) {
                $the_account = account::lockForUpdate()->find($account->id);
                $the_account->balance = $the_account->balance - $cash_out->amount;
                $the_account->save();
            });

            return redirect()->route('accounts.payable')->with('success', 'Payment successful');
        }
    }

    public function index(Request $request)
    {
        $query = operators_online_payment::query();

        if ($request->has('date_range')) {
            $dates = explode(' - ', $request->input('date_range'));
            $query->whereBetween('date', [$dates[0], $dates[1]]);
        }

        if ($request->has('operator')) {
            $query->where('operator_id', $request->input('operator'));
        }

        if ($request->has('gateway')) {
            $query->where('payment_gateway_name', $request->input('gateway'));
        }

        if ($request->has('purpose')) {
            $query->where('payment_purpose', $request->input('purpose'));
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('transaction_id', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('operator_name', 'like', '%' . $request->input('search') . '%');
            });
        }

        $payments = $query->get();
        $operators = Operator::all();
        $gateways = operators_online_payment::select('payment_gateway_name')->distinct()->pluck('payment_gateway_name');
        $purposes = operators_online_payment::select('payment_purpose')->distinct()->pluck('payment_purpose');

        return view('payment_statement.index', compact('payments', 'operators', 'gateways', 'purposes'));
    }

    public function generateReport(Request $request)
    {
        $query = operators_online_payment::query();
        
        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }
        
        if ($request->filled('gateway')) {
            $query->where('payment_gateway_name', $request->gateway);
        }

        $payments = $query->get();

        $report = [];
        foreach ($payments as $payment) {
            $report[] = [
                'Operator ID' => $payment->operator_id,
                'Account ID' => $payment->account_id,
                'Payment Purpose' => $payment->payment_purpose,
                'Amount' => $payment->amount_paid,
                'Store Amount' => $payment->store_amount,
                'Transaction Fee' => $payment->transaction_fee,
                'Payment Gateway' => $payment->payment_gateway_name,
                'Status' => $payment->pay_status,
                'Date' => $payment->date,
            ];
        }

        // Generate CSV file to download
        $filename = 'operators_online_payment_report.csv';
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array_keys($report[0]));

        foreach ($report as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }
}
