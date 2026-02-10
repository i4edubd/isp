<?php

namespace App\Http\Controllers;

use App\Models\bills_vs_payments_chart;
use App\Models\customer_bill;
use App\Models\customer_payment;
use App\Models\operator;
use App\Models\operators_income;
use Illuminate\Http\Request;

class BillsVsPaymentsChartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $allowed_roles = ['group_admin', 'operator', 'sub_operator'];

        if (in_array($operator->role, $allowed_roles) == false) {
            return 0;
        }

        $bills_n_payments = bills_vs_payments_chart::where('operator_id', $operator->id)->get();
        $bills = $bills_n_payments->where('topic', 'bill');
        $payments = $bills_n_payments->where('topic', 'payment');

        $total = $bills_n_payments->sum('amount') > 0 ? $bills_n_payments->sum('amount') : 1;
        $total_due = $bills->sum('amount');
        $total_payment = $payments->sum('amount');
        $total_due_percentage = round(($total_due / $total) * 100);
        $total_payment_percentage = 100 - $total_due_percentage;

        if ($bills->count() !== $payments->count()) {
            return 0;
        }

        $labels = [];

        $bill_data = [];

        $payment_data = [];

        foreach ($bills as $bill) {
            $label = $bill->source_operator_id . "::" . $bill->source_operator_name;
            $labels[] = $label;
            $payment = $payments->where('source_operator_id', $bill->source_operator_id)->first();
            $bill_data[] = $bill->amount;
            $payment_data[] = $payment->amount;
        }

        $data = [];
        $data['labels'] = $labels;
        $data['bill_data'] = $bill_data;
        $data['payment_data'] = $payment_data;
        $data['total_due'] = $total_due;
        $data['total_payment'] = $total_payment;
        $data['total_due_percentage'] = $total_due_percentage;
        $data['total_payment_percentage'] = $total_payment_percentage;

        return json_encode($data);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\operator $operator
     * @return \Illuminate\Http\Response
     */
    public static function store(operator $operator)
    {
        $allowed_roles = ['group_admin', 'operator', 'sub_operator'];

        if (in_array($operator->role, $allowed_roles) == false) {
            return 0;
        }

        // cleaning
        bills_vs_payments_chart::where('operator_id', $operator->id)->delete();

        // group_admin
        if ($operator->role == 'group_admin') {

            # 1.1 >> from group_admin || Bills
            $bill_amount = customer_bill::where('mgid', $operator->id)
                ->where('operator_id', $operator->id)
                ->sum('amount');

            self::storeTuple($operator, $operator, 'bill', $bill_amount);

            # 1.2 >> from group_admin || Payments collection
            $paid_amount = customer_payment::where('operator_id', $operator->id)
                ->where('month', date(config('app.month_format')))
                ->where('year', date(config('app.year_format')))
                ->where('pay_status', 'Successful')
                ->sum('store_amount');

            self::storeTuple($operator, $operator, 'payment', $paid_amount);

            # 2 >> from operator
            $group_admins_source_operators = operator::where('mgid', $operator->id)->get();

            $source_operators = $group_admins_source_operators->filter(function ($operator) {
                return $operator->role == 'operator';
            });

            foreach ($source_operators as $source_operator) {

                # 2.1 >> Bills
                $bill_amount = customer_bill::where('mgid', $operator->id)
                    ->where('operator_id', $source_operator->id)
                    ->sum('operator_amount');

                self::storeTuple($operator, $source_operator, 'bill', $bill_amount);

                # 2.2 >> Payments collection
                $paid_amount = operators_income::where('operator_id', $operator->id)
                    ->where('source_operator_id', $source_operator->id)
                    ->where('month', date(config('app.month_format')))
                    ->where('year', date(config('app.year_format')))
                    ->sum('amount');

                self::storeTuple($operator, $source_operator, 'payment', $paid_amount);
            }

            # 3 >> from sub_operator
            $source_operators = $group_admins_source_operators->filter(function ($operator) {
                return $operator->role == 'sub_operator';
            });

            foreach ($source_operators as $source_operator) {

                # 3.1 >> Bills
                $sub_reseller_bills = customer_bill::where('mgid', $operator->id)
                    ->where('operator_id', $source_operator->id)
                    ->get();

                $sub_reseller_bills = $sub_reseller_bills->groupBy('package_id');

                $amount = 0;

                foreach ($sub_reseller_bills as $package_id => $bills) {
                    $package = CacheController::getPackage($package_id);
                    $parent_package = $package->parent_package;
                    $bill_count = $bills->count();
                    $amount = $amount + $bill_count * $parent_package->operator_price;
                }

                self::storeTuple($operator, $source_operator, 'bill', $amount);

                # 3.2 >> Payments collection
                $paid_amount = operators_income::where('operator_id', $operator->id)
                    ->where('source_operator_id', $source_operator->id)
                    ->where('month', date(config('app.month_format')))
                    ->where('year', date(config('app.year_format')))
                    ->sum('amount');

                self::storeTuple($operator, $source_operator, 'payment', $paid_amount);
            }
        }

        // operator
        if ($operator->role == 'operator') {

            # 1 >> from operator || Bills
            $bill_amount = customer_bill::where('operator_id', $operator->id)->sum('amount');

            self::storeTuple($operator, $operator, 'bill', $bill_amount);

            # 1 >> from operator || Payments collection
            $paid_amount = customer_payment::where('operator_id', $operator->id)
                ->where('month', date(config('app.month_format')))
                ->where('year', date(config('app.year_format')))
                ->where('pay_status', 'Successful')
                ->sum('store_amount');

            self::storeTuple($operator, $operator, 'payment', $paid_amount);

            # 2 >> from sub_operator
            $operators_source_operators = operator::where('gid', $operator->id)->get();

            $source_operators = $operators_source_operators->filter(function ($operator) {
                return $operator->role == 'sub_operator';
            });

            foreach ($source_operators as $source_operator) {

                # 2.1 >> Bills
                $bill_amount = customer_bill::where('gid', $operator->id)
                    ->where('operator_id', $source_operator->id)
                    ->sum('operator_amount');

                self::storeTuple($operator, $source_operator, 'bill', $bill_amount);

                # 2.2 >> Payments collection
                $paid_amount = operators_income::where('operator_id', $operator->id)
                    ->where('source_operator_id', $source_operator->id)
                    ->where('month', date(config('app.month_format')))
                    ->where('year', date(config('app.year_format')))
                    ->sum('amount');

                self::storeTuple($operator, $source_operator, 'payment', $paid_amount);
            }
        }

        // sub_operator

        if ($operator->role == 'sub_operator') {

            # 1 >> from sub_operator || Bills
            $bill_amount = customer_bill::where('operator_id', $operator->id)->sum('amount');

            self::storeTuple($operator, $operator, 'bill', $bill_amount);

            # 1 >> from sub_operator || Payments collection
            $paid_amount = customer_payment::where('operator_id', $operator->id)
                ->where('month', date(config('app.month_format')))
                ->where('year', date(config('app.year_format')))
                ->where('pay_status', 'Successful')
                ->sum('store_amount');

            self::storeTuple($operator, $operator, 'payment', $paid_amount);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\operator $operator
     * @param  \App\Models\operator $source_operator
     * @param  string $topic
     * @param  string $amount
     * @return  int
     */
    public static function storeTuple(operator $operator, operator $source_operator, $topic, $amount)
    {
        $bills_vs_payments_chart = new bills_vs_payments_chart();
        $bills_vs_payments_chart->operator_id = $operator->id;
        $bills_vs_payments_chart->source_operator_id = $source_operator->id;
        $bills_vs_payments_chart->source_operator_name = $source_operator->name;
        $bills_vs_payments_chart->topic = $topic;
        $bills_vs_payments_chart->amount = $amount;
        $bills_vs_payments_chart->save();
        return $bills_vs_payments_chart->id;
    }
}
