<?php

namespace App\Http\Controllers;

use App\Models\expense;
use App\Models\operators_income;
use Illuminate\Http\Request;

class IncomeVsExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $year = date(config('app.year_format'));

        if ($request->filled('year')) {
            $year = $request->year;
        }

        $incomes = operators_income::where('operator_id', $operator->id)
            ->where('year', $year)
            ->get();

        $expenses = expense::where('operator_id', $operator->id)
            ->where('year', $year)
            ->get();

        $summary_report = [];

        for ($i = 1; $i <= 12; $i++) {
            $row = [];
            $month = date_format(date_create(date('01-' . $i . '-Y')), config('app.month_format'));
            $row['year'] = $year;
            $row['month'] = $month;
            $row['income'] = $incomes->where('month', $month)->sum('amount');
            $row['expense'] = $expenses->where('month', $month)->sum('amount');
            $row['balance'] = $row['income'] - $row['expense'];
            if ($row['income'] > 0 || $row['expense'] > 0) {
                $summary_report[] = $row;
            }
        }

        switch ($operator->role) {

            case 'super_admin':
                return view('admins.super_admin.income-vs-expense-report', [
                    'year' => $year,
                    'summary_report' => $summary_report,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.income-vs-expense-report', [
                    'year' => $year,
                    'summary_report' => $summary_report,
                ]);
                break;

            case 'operator':
                return view('admins.operator.income-vs-expense-report', [
                    'year' => $year,
                    'summary_report' => $summary_report,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.income-vs-expense-report', [
                    'year' => $year,
                    'summary_report' => $summary_report,
                ]);
                break;
        }
    }
}
