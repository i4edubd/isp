<?php

namespace App\Http\Controllers;

use App\Models\yearly_operators_income;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class OperatorsIncomeSummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $cache_key = 'operators_income_summary' . $operator->id;

        $ttl = 200;

        $operator_incomes = Cache::remember($cache_key, $ttl, function () use ($operator) {
            return yearly_operators_income::where('operator_id', $operator->id)
                ->get()
                ->sortByDesc('year');
        });

        if ($request->filled('year')) {
            $operator_incomes = $operator_incomes->where('year', $request->year);
        }

        if ($request->filled('month')) {
            $operator_incomes = $operator_incomes->where('month', $request->month);
        }

        // total_amount
        $total_amount = $operator_incomes->sum('amount');

        // length
        $length = 50;

        if ($request->filled('length')) {
            $length = $request->length;
        }

        $current_page = $request->input("page") ?? 1;

        $view_incomes = new LengthAwarePaginator($operator_incomes->forPage($current_page, $length), $operator_incomes->count(), $length, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.operator_incomes-summary', [
                    'operator_incomes' => $view_incomes,
                    'length' => $length,
                    'total_amount' => $total_amount,
                ]);
                break;

            case 'operator':
                return view('admins.operator.operator_incomes-summary', [
                    'operator_incomes' => $view_incomes,
                    'length' => $length,
                    'total_amount' => $total_amount,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.operator_incomes-summary', [
                    'operator_incomes' => $view_incomes,
                    'length' => $length,
                    'total_amount' => $total_amount,
                ]);
                break;
        }
    }
}
