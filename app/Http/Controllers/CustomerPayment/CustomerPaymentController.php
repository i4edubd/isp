<?php

namespace App\Http\Controllers\CustomerPayment;

use App\Http\Controllers\Controller;
use App\Models\customer_payment;
use App\Models\operator;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class CustomerPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

        //operator
        $requester = $request->user();

        // operator_id
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

        $cache_key = 'customer_payments_' . $operator_id;

        $ttl = 300;

        if ($request->filled('refresh')) {
            if (Cache::has($cache_key)) {
                Cache::forget($cache_key);
            }
        }

        $customer_payments = Cache::remember($cache_key, $ttl, function () use ($requester, $operator_id) {
            if ($requester->role == 'super_admin') {
                return customer_payment::where('mgid', $operator_id)
                    ->where('pay_status', 'Successful')
                    ->where('type', 'Online')
                    ->orderBy('id', 'desc')
                    ->limit(1000)
                    ->get();
            } else {
                return customer_payment::where('operator_id', $operator_id)
                    ->where('pay_status', 'Successful')
                    ->orderBy('id', 'desc')
                    ->limit(1000)
                    ->get();
            }
        });

        // type
        if ($request->filled('type')) {
            $type = $request->type;
            $customer_payments = $customer_payments->filter(function ($payment) use ($type) {
                return $payment->type == $type;
            });
        }

        // year
        if ($request->filled('year')) {
            $year = $request->year;
            $customer_payments = $customer_payments->filter(function ($payment) use ($year) {
                return $payment->year == $year;
            });
        }

        // month
        if ($request->filled('month')) {
            $month = $request->month;
            $customer_payments = $customer_payments->filter(function ($payment) use ($month) {
                return $payment->month == $month;
            });
        }

        // date
        if ($request->filled('date')) {
            $date = date_format(date_create($request->date), config('app.date_format'));
            $customer_payments = $customer_payments->filter(function ($payment) use ($date) {
                return $payment->date == $date;
            });
        }

        // cash_collector_id
        if ($request->filled('cash_collector_id')) {
            $cash_collector_id = $request->cash_collector_id;
            $customer_payments = $customer_payments->filter(function ($payment) use ($cash_collector_id) {
                return $payment->cash_collector_id == $cash_collector_id;
            });
        }

        // note
        if ($request->filled('note')) {
            $note = $request->note;
            $customer_payments = $customer_payments->filter(function ($payment) use ($note) {
                return false !== stristr($payment->note, $note);
            });
        }

        $total_amount = $customer_payments->sum('amount_paid');
        $first_party = $customer_payments->sum('first_party');
        $second_party = $customer_payments->sum('second_party');
        $third_party = $customer_payments->sum('third_party');

        // default length
        $length = 10;

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
                $group_admins = operator::where('role', 'group_admin')->get();
                return view('admins.super_admin.customers-payments', [
                    'payments' => $payments,
                    'group_admins' => $group_admins,
                    'total_amount' => $total_amount,
                    'first_party' => $first_party,
                    'second_party' => $second_party,
                    'third_party' => $third_party,
                    'length' => $length,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.customers-payments', [
                    'payments' => $payments,
                    'total_amount' => $total_amount,
                    'first_party' => $first_party,
                    'second_party' => $second_party,
                    'third_party' => $third_party,
                    'length' => $length,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customers-payments', [
                    'payments' => $payments,
                    'total_amount' => $total_amount,
                    'first_party' => $first_party,
                    'second_party' => $second_party,
                    'third_party' => $third_party,
                    'length' => $length,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customers-payments', [
                    'payments' => $payments,
                    'total_amount' => $total_amount,
                    'first_party' => $first_party,
                    'second_party' => $second_party,
                    'third_party' => $third_party,
                    'length' => $length,
                ]);
                break;

            case 'manager':
                return view('admins.manager.customers-payments', [
                    'payments' => $payments,
                    'total_amount' => $total_amount,
                    'first_party' => $first_party,
                    'second_party' => $second_party,
                    'third_party' => $third_party,
                    'length' => $length,
                ]);
                break;
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  string  $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $customer_payment)
    {
        $request->validate([
            'fieldname' => 'required|in:mobile,username'
        ]);

        $operator = $request->user();
        $payments =  match ($operator->role) {
            'group_admin' => customer_payment::where('mgid', $operator->id)->where([
                [$request->fieldname, '=', $customer_payment]
            ])->get(),
            'manager' => customer_payment::where('operator_id', $operator->gid)->where([
                [$request->fieldname, '=', $customer_payment]
            ])->get(),
            default => customer_payment::where('operator_id', $operator->id)->where([
                [$request->fieldname, '=', $customer_payment]
            ])->get(),
        };

        if (count($payments)) {
            return view('admins.components.customer-payments-search-result', [
                'payments' => $payments,
            ]);
        } else {
            return 'No Transaction Found';
        }
    }
}
