<?php

namespace App\Http\Controllers;

use App\Jobs\BulkCustomerBillPaidJob;
use App\Models\bulk_customer_bill_paid;
use Illuminate\Http\Request;

class BulkCustomerBillPaidController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        // function variables
        $operator = $request->user();

        $has_balance = 0;

        $balance = 0;

        $remaining_balance = 0;

        // bills
        $bills = bulk_customer_bill_paid::where('requester_id', $request->user()->id)->get();

        $bill_count = $bills->count();

        if ($bill_count == 0) {
            return redirect()->route('customer_bills.index');
        }

        $roles = ['group_admin', 'operator', 'sub_operator'];

        if (!in_array($operator->role, $roles)) {
            return redirect()->route('customer_bills.index');
        }

        $customer_amount = $bills->sum('amount');

        $operator_amount = $bills->sum('operator_amount');

        switch ($operator->role) {

            case 'group_admin':
                $balance = 'N/A';
                $remaining_balance = "N/A";
                $has_balance = 1;
                break;

            case 'operator':
            case 'sub_operator':
                if ($operator->account_type == 'debit') {
                    $balance = $operator->account_balance;
                    $remaining_balance = $balance - $operator_amount;
                    $has_balance = $remaining_balance >= 0 ? 1 : 0;
                } else {
                    if ($operator->credit_limit > 0) {
                        $balance = $operator->credit_balance;
                        $remaining_balance = $balance - $operator_amount;
                        $has_balance = $remaining_balance >= 0 ? 1 : 0;
                    } else {
                        $balance = $operator->credit_balance;
                        $remaining_balance = "N/A";
                        $has_balance = 1;
                    }
                }
                break;
        }

        if ($has_balance == 1) {

            switch ($operator->role) {
                case 'group_admin':
                    return view('admins.group_admin.process-multiple-customer-bills', [
                        'bill_count' => $bill_count,
                        'customers_amount' => $customer_amount,
                        'operator_amount' => $operator_amount,
                        'balance' => $remaining_balance,
                    ]);
                    break;

                case 'operator':
                    return view('admins.operator.process-multiple-customer-bills', [
                        'bill_count' => $bill_count,
                        'customers_amount' => $customer_amount,
                        'operator_amount' => $operator_amount,
                        'balance' => $remaining_balance,
                    ]);
                    break;

                case 'sub_operator':
                    return view('admins.sub_operator.process-multiple-customer-bills', [
                        'bill_count' => $bill_count,
                        'customers_amount' => $customer_amount,
                        'operator_amount' => $operator_amount,
                        'balance' => $remaining_balance,
                    ]);
                    break;
            }
        } else {

            $error_msg = "Required Balance: " . $operator_amount . " . But you have: " . $balance;

            return redirect()->route('customer_bills.index')->with('error', $error_msg);
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
        BulkCustomerBillPaidJob::dispatch($request->user())
            ->onConnection('database')
            ->onQueue('bulk_customer_bill_paid');

        return redirect()->route('customers.index')->with('success', 'Job is Processing! Please Wait');
    }
}
