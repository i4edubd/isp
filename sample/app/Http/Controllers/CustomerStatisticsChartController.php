<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CustomerStatisticsChartController extends Controller
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

        $key = 'customer_statistics_' . $operator->id;

        $ttl = 200;

        return Cache::remember($key, $ttl, function () use ($operator) {

            switch ($operator->role) {
                case 'group_admin':
                    $operators = operator::where('mgid', $operator->id)->get();
                    break;

                case 'operator':
                case 'sub_operator':
                    $operators = operator::where('id', $operator->id)->orWhere('gid', $operator->id)->get();
                    break;
            }

            $operators = $operators->filter(function ($operator) {
                return $operator->role == 'group_admin' || $operator->role == 'operator' || $operator->role == 'sub_operator';
            });

            $labels = [];

            $operators_customer_count = [];

            $operators_paid_customer_count = [];

            $operators_billed_customer_count = [];

            $operators_active_customer_count = [];

            $operators_suspended_customer_count = [];

            $operators_disabled_customer_count = [];

            $operators_online_customer_count = [];

            $operators_offline_customer_count = [];

            $total_customer_count = 0;

            $total_paid_customers_count = 0;

            $total_billed_customers_count = 0;

            $total_active_customer_count = 0;

            $total_suspended_customer_count = 0;

            $total_disabled_customer_count = 0;

            $total_online_customer_count = 0;

            $total_offline_customer_count = 0;

            $date_labels = [];

            $daily_new_customers_count = [];

            for ($i = 1; $i <= date('t'); $i++) {
                $daily_new_customers_count[date($i . '-m-Y')] = 0;
            }

            $date_labels = array_keys($daily_new_customers_count);

            $operators_monthly_new_customers_count = [];

            foreach ($operators as $operator) {

                $labels[] = $operator->id . "::" . $operator->name;

                $customers = customer::with('radaccts')->where('operator_id', $operator->id)->get();

                // customer_count
                $customer_count = $customers->count();
                $operators_customer_count[] = $customer_count;
                $total_customer_count = $total_customer_count + $customer_count;

                // paid_customer_count
                $paid_customer_count = $customers->where('payment_status', 'paid')->count();
                $operators_paid_customer_count[] = $paid_customer_count;
                $total_paid_customers_count = $total_paid_customers_count + $paid_customer_count;

                // billed_customer_count
                $billed_customer_count = $customers->where('payment_status', 'billed')->count();
                $operators_billed_customer_count[] = $billed_customer_count;
                $total_billed_customers_count = $total_billed_customers_count + $billed_customer_count;

                // active_customer_count
                $active_customer_count = $customers->where('status', 'active')->count();
                $operators_active_customer_count[] = $active_customer_count;
                $total_active_customer_count = $total_active_customer_count + $active_customer_count;

                // suspended_customer_count
                $suspended_customer_count = $customers->where('status', 'suspended')->count();
                $operators_suspended_customer_count[] = $suspended_customer_count;
                $total_suspended_customer_count = $total_suspended_customer_count + $suspended_customer_count;

                // disabled_customer_count
                $disabled_customer_count = $customers->where('status', 'disabled')->count();
                $operators_disabled_customer_count[] = $disabled_customer_count;
                $total_disabled_customer_count = $total_disabled_customer_count + $disabled_customer_count;

                $online_customers = $customers->filter(function ($customer) {
                    return $customer->radaccts->whereNull('acctstoptime')->count() > 0;
                });

                // online_customer_count
                $online_customer_count = $online_customers->count();
                $operators_online_customer_count[] = $online_customer_count;
                $total_online_customer_count = $total_online_customer_count + $online_customer_count;

                // offline_customers_count
                $offline_customers_count = $customer_count - $online_customer_count;
                $operators_offline_customer_count[] = $offline_customers_count;
                $total_offline_customer_count = $total_offline_customer_count + $offline_customers_count;

                // new_customers_count
                $operators_monthly_new_customers_count[] = $customers->where('registration_year', date(config('app.year_format')))
                    ->where('registration_month', date(config('app.month_format')))->count();

                foreach ($daily_new_customers_count as $key => $value) {
                    $daily_count = $customers->where('registration_date', date_format(date_create($key), config('app.date_format')))->count();
                    $daily_new_customers_count[$key] = $value + $daily_count;
                }
            }

            $statistics = [
                'labels' => $labels,
                'operators_paid_customer_count' => $operators_paid_customer_count,
                'operators_billed_customer_count' => $operators_billed_customer_count,
                'operators_customer_count' => $operators_customer_count,
                'operators_active_customer_count' => $operators_active_customer_count,
                'operators_suspended_customer_count' => $operators_suspended_customer_count,
                'operators_disabled_customer_count' => $operators_disabled_customer_count,
                'operators_online_customer_count' => $operators_online_customer_count,
                'operators_offline_customer_count' => $operators_offline_customer_count,
                'total_customer_count' => $total_customer_count,
                'total_paid_customers_count' => $total_paid_customers_count,
                'total_billed_customers_count' => $total_billed_customers_count,
                'total_active_customer_count' => $total_active_customer_count,
                'total_suspended_customer_count' => $total_suspended_customer_count,
                'total_disabled_customer_count' => $total_disabled_customer_count,
                'total_online_customer_count' => $total_online_customer_count,
                'total_offline_customer_count' => $total_offline_customer_count,
                'operators_monthly_new_customers_count' => $operators_monthly_new_customers_count,
                'date_labels' => $date_labels,
                'daily_new_customers_count' => array_values($daily_new_customers_count),
            ];

            return json_encode($statistics);
        });
    }
}
