<?php

namespace App\Http\Controllers;

use App\Models\customer_payment;
use App\Models\payment_gateway;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Spatie\SimpleExcel\SimpleExcelWriter;

class PaymentGatewayPaymentsViewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\payment_gateway  $payment_gateway
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, payment_gateway $payment_gateway)
    {
        $requester = $request->user();

        if ($requester->id !== $payment_gateway->operator_id) {
            abort(404);
        }

        $cache_key = 'payment_gateway_customer_payments_' . $payment_gateway->id;

        $ttl = 300;

        if ($request->filled('refresh')) {
            if (Cache::has($cache_key)) {
                Cache::forget($cache_key);
            }
        }

        $customer_payments = Cache::remember($cache_key, $ttl, function () use ($payment_gateway) {
            return customer_payment::where('payment_gateway_id', $payment_gateway->id)
                ->where('pay_status', 'Successful')
                ->orderBy('id', 'desc')
                ->limit(1000)
                ->get();
        });

        // default length
        $length = 50;

        if ($request->filled('length')) {
            $length = $request->length;
        }

        $current_page = $request->input("page") ?? 1;

        $payments = new LengthAwarePaginator($customer_payments->forPage($current_page, $length), $customer_payments->count(), $length, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        switch ($requester->role) {

            case 'super_admin':
                return view('admins.super_admin.payment-gateway-customers-payments', [
                    'payment_gateway' => $payment_gateway,
                    'payments' => $payments,
                    'length' => $length,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.payment-gateway-customers-payments', [
                    'payment_gateway' => $payment_gateway,
                    'payments' => $payments,
                    'length' => $length,
                ]);
                break;

            case 'operator':
                return view('admins.operator.payment-gateway-customers-payments', [
                    'payment_gateway' => $payment_gateway,
                    'payments' => $payments,
                    'length' => $length,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.payment-gateway-customers-payments', [
                    'payment_gateway' => $payment_gateway,
                    'payments' => $payments,
                    'length' => $length,
                ]);
                break;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\payment_gateway  $payment_gateway
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, payment_gateway $payment_gateway)
    {

        $operator = $request->user();

        if ($operator->id !== $payment_gateway->operator_id) {
            abort(404);
        }

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.payment-gateway-customer-payments-download-create', [
                    'payment_gateway' => $payment_gateway,
                ]);
                break;

            case 'operator':
                return view('admins.operator.payment-gateway-customer-payments-download-create', [
                    'payment_gateway' => $payment_gateway,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.payment-gateway-customer-payments-download-create', [
                    'payment_gateway' => $payment_gateway,
                ]);
                break;

            case 'super_admin':
                return view('admins.super_admin.payment-gateway-customer-payments-download-create', [
                    'payment_gateway' => $payment_gateway,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\payment_gateway  $payment_gateway
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, payment_gateway $payment_gateway)
    {
        $operator = $request->user();

        if ($operator->id !== $payment_gateway->operator_id) {
            abort(404);
        }

        $filter = [];

        // Default filter
        $filter[] = ['pay_status', '=', 'Successful'];
        $filter[] = ['payment_gateway_id', '=', $payment_gateway->id];

        // year
        if ($request->filled('year')) {
            $filter[] = ['year', '=', $request->year];
        }

        // month
        if ($request->filled('month')) {
            $filter[] = ['month', '=', $request->month];
        }

        $payments = customer_payment::where($filter)->get();

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
                'customer ID' => $payment->customer_id,
                'Mobile' => $payment->mobile,
                'Username' => $payment->username,
                'Payment Gateway' => $payment->payment_gateway_name,
                'status' => $payment->pay_status,
                'Amount Paid' => $payment->amount_paid,
                'Transaction Fee' => $payment->transaction_fee,
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
            ]);
        }

        $writer->addRow([
            'Admin ID' => "",
            'Operator ID' => "",
            'customer ID' => "",
            'Mobile' => "",
            'Username' => "",
            'Payment Gateway' => "",
            'status' => "Total",
            'Amount Paid' => $total_paid,
            'Transaction Fee' => $total_fee,
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
        ]);

        $writer->toBrowser();
    }
}
