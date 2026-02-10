<?php

namespace App\Http\Controllers;

use App\Models\operator_payment;
use App\Models\operator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OperatorSubOperatorPaymentsController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'operator_id' => 'nullable|numeric',
            'type' => 'nullable|in:Cash,Online,RechargeCard',
            'year' => 'nullable|numeric',
            'month' => 'nullable',
            'length' => 'nullable|numeric',
            'cash_collector_id' => 'nullable|numeric',
        ]);

        $requester = $request->user();

        if ($request->filled('operator_id')) {
            $operator_id = $request->operator_id;
            $requested_operator = operator::find($operator_id);
            $this->authorize('view', $requested_operator);
        } else {
            if ($requester->role == 'manager') {
                $operator_id = $requester->group_admin->id;
            } else {
                $operator_id = $requester->id;
            }
        }

        // Build cache key that includes latest ids so new rows invalidate the cache automatically
        $last_op_payment_id = operator_payment::where('operator_id', $operator_id)->max('id') ?? 0;
        $last_online_payment_id = DB::table('operators_online_payments')->where('operator_id', $operator_id)->max('id') ?? 0;
        $cache_key = 'operator_payments_' . $operator_id . '_' . $last_op_payment_id . '_' . $last_online_payment_id;
        $ttl = 300;

        if ($request->filled('refresh')) {
            if (Cache::has($cache_key)) {
                Cache::forget($cache_key);
            }
        }

        // Cache a merged collection consisting of operator_payment rows and mapped operators_online_payments rows
        $operator_payments = Cache::remember($cache_key, $ttl, function () use ($operator_id) {

            // Primary operator_payments (existing records)
            $payments1 = operator_payment::where('operator_id', $operator_id)
                ->where('pay_status', 'Successful')
                ->orderBy('id', 'desc')
                ->limit(1000)
                ->get();

            // Operators online payments (e.g., bKash topups). Map to similar shape expected by the view.
            $payments2 = DB::table('operators_online_payments')
                ->select([
                    'id',
                    'operator_id',
                    'payment_gateway_id',
                    'payment_gateway_name',
                    'payment_purpose',
                    'pay_status',
                    'amount_paid',
                    'store_amount',
                    'transaction_fee',
                    'pgw_payment_identifier',
                    'mer_txnid',
                    'pgw_txnid',
                    'bank_txnid',
                    'card_type',
                    'card_number',
                    'payment_token',
                    'note',
                    'date',
                    'month',
                    'year',
                    'created_at',
                    'updated_at'
                ])
                ->where('operator_id', $operator_id)
                ->where('pay_status', 'Successful')
                ->orderBy('id', 'desc')
                ->limit(1000)
                ->get()
                ->map(function ($row) {
                    // Map DB row to an object with properties the views expect
                    return (object)[
                        'id' => $row->id,
                        'operator_id' => $row->operator_id,
                        'mobile' => null,
                        'username' => null,
                        'type' => 'Online', // align with UI 'type' filter options
                        'amount_paid' => $row->amount_paid,
                        'store_amount' => $row->store_amount,
                        'transaction_fee' => $row->transaction_fee,
                        'discount' => null,
                        'vat_paid' => null,
                        'first_party' => null,
                        'second_party' => null,
                        'third_party' => null,
                        'payment_gateway_name' => $row->payment_gateway_name ?? null,
                        'pay_status' => $row->pay_status,
                        'date' => $row->date,
                        'month' => $row->month,
                        'year' => $row->year,
                        'note' => $row->note ?? null,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                        // include PGW fields if needed for exports/search
                        'pgw_payment_identifier' => $row->pgw_payment_identifier ?? null,
                        'mer_txnid' => $row->mer_txnid ?? null,
                        'pgw_txnid' => $row->pgw_txnid ?? null,
                        'bank_txnid' => $row->bank_txnid ?? null,
                    ];
                });

            // Merge and sort by created_at (desc). Use created_at if available, else id as fallback.
            $merged = $payments1->concat($payments2)->sortByDesc(function ($p) {
                // created_at may be string or Carbon; normalize fallback to id
                return $p->created_at ?? ($p->id ?? 0);
            })->values();

            return $merged;
        });

        // Apply filters on merged collection (as before)
        if ($request->filled('type')) {
            $type = $request->type;
            $operator_payments = $operator_payments->filter(function ($payment) use ($type) {
                return $payment->type == $type;
            });
        }

        if ($request->filled('year')) {
            $year = $request->year;
            $operator_payments = $operator_payments->filter(function ($payment) use ($year) {
                return $payment->year == $year;
            });
        }

        if ($request->filled('month')) {
            $month = $request->month;
            $operator_payments = $operator_payments->filter(function ($payment) use ($month) {
                return $payment->month == $month;
            });
        }

        if ($request->filled('date')) {
            $date = date_format(date_create($request->date), config('app.date_format'));
            $operator_payments = $operator_payments->filter(function ($payment) use ($date) {
                return $payment->date == $date;
            });
        }

        if ($request->filled('cash_collector_id')) {
            $cash_collector_id = $request->cash_collector_id;
            $operator_payments = $operator_payments->filter(function ($payment) use ($cash_collector_id) {
                // operators_online_payments don't have cash_collector_id; treated as 0 (no match) unless you set it elsewhere
                return isset($payment->cash_collector_id) && $payment->cash_collector_id == $cash_collector_id;
            });
        }

        if ($request->filled('note')) {
            $note = $request->note;
            $operator_payments = $operator_payments->filter(function ($payment) use ($note) {
                return false !== stristr($payment->note ?? '', $note);
            });
        }

        $total_amount = $operator_payments->sum('amount_paid');
        $first_party = $operator_payments->sum('first_party');
        $second_party = $operator_payments->sum('second_party');
        $third_party = $operator_payments->sum('third_party');

        $length = $request->filled('length') ? $request->length : 10;
        $current_page = $request->input("page") ?? 1;

        $payments = new LengthAwarePaginator($operator_payments->forPage($current_page, $length), $operator_payments->count(), $length, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        switch ($requester->role) {
            case 'super_admin':
                $group_admins = operator::where('role', 'group_admin')->get();
                return view('admins.super_admin.operators-payments', compact('payments', 'group_admins', 'total_amount', 'first_party', 'second_party', 'third_party', 'length'));
            case 'group_admin':
                return view('admins.group_admin.operators-payments', compact('payments', 'total_amount', 'first_party', 'second_party', 'third_party', 'length'));
            case 'operator':
                return view('admins.operator.operators-payments', compact('payments', 'total_amount', 'first_party', 'second_party', 'third_party', 'length'));
            case 'sub_operator':
                return view('admins.sub_operator.operators-payments', compact('payments', 'total_amount', 'first_party', 'second_party', 'third_party', 'length'));
            case 'manager':
                return view('admins.manager.operators-payments', compact('payments', 'total_amount', 'first_party', 'second_party', 'third_party', 'length'));
        }
    }

    public function show(Request $request, string $operator_payment)
    {
        $request->validate([
            'fieldname' => 'required|in:mobile,username'
        ]);

        $operator = $request->user();
        $payments = match ($operator->role) {
            'group_admin' => operator_payment::where('mgid', $operator->id)->where($request->fieldname, $operator_payment)->get(),
            'manager' => operator_payment::where('operator_id', $operator->gid)->where($request->fieldname, $operator_payment)->get(),
            default => operator_payment::where('operator_id', $operator->id)->where($request->fieldname, $operator_payment)->get(),
        };

        if (count($payments)) {
            return view('admins.components.operator-payments-search-result', compact('payments'));
        } else {
            return 'No Transaction Found';
        }
    }
}
