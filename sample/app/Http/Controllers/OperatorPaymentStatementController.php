<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\operators_online_payment;
use App\Models\Operator;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class OperatorPaymentStatementController extends Controller
{
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

        $payments = $query->paginate(10);
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
