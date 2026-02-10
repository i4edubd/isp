<?php

namespace App\Http\Controllers;

use App\Models\account;
use App\Models\expense;
use Carbon\Carbon;
use App\Models\operators_income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Dashboard
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request)
    {
        $role = $request->user()->role;

        switch ($role) {
            case 'super_admin':
                return view('admins.super_admin.dashboard');
                break;

            case 'group_admin':
                $accounts_receivable = account::where('account_owner', $request->user()->id)->sum('balance');
                $accounts_payable = account::where('account_provider', $request->user()->id)->sum('balance');
                return view('admins.group_admin.dashboard', [
                    'accounts_receivable' => $accounts_receivable,
                    'accounts_payable' => $accounts_payable,
                ]);
                break;

            case 'operator':
                return view('admins.operator.dashboard');
                break;

            case 'sub_operator':
                return view('admins.sub_operator.dashboard');
                break;

            case 'manager':
                return view('admins.manager.dashboard');
                break;

            case 'developer':
                return view('admins.developer.dashboard');
                break;

            case 'sales_manager':
                return view('admins.sales_manager.dashboard');
                break;
        }
    }


    /**
     * Income Chart
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function dashboardChart(Request $request)
    {
        $operator = $request->user();

        $key = 'dashboardchart_' . $operator->id;

        $ttl = 300;

        return Cache::remember($key, $ttl, function () use ($operator) {

            $income_chart = [];
            $expense_chart = [];

            $where = [
                ['operator_id', '=', $operator->id],
                ['year', '=', date(config('app.year_format'))]
            ];

            $operator_incomes = operators_income::where($where)->get();
            for ($i = 1; $i <= 12; $i++) {
                $month = Carbon::create(date('01-' . $i . '-Y'))->format(config('app.month_format'));
                $income_chart[$month] = $operator_incomes->where('month', $month)->sum('amount');
            }

            $expensees = expense::where($where)->get();
            for ($i = 1; $i <= 12; $i++) {
                $month = Carbon::create(date('01-' . $i . '-Y'))->format(config('app.month_format'));
                $expense_chart[$month] = $expensees->where('month', $month)->sum('amount');
            }

            $chart = [];
            $chart['in'] = $income_chart;
            $chart['out'] = $expense_chart;

            return json_encode($chart);
        });
    }
}
