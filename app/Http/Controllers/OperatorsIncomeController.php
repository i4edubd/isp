<?php

namespace App\Http\Controllers;

use App\Models\operator;
use App\Models\operators_income;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class OperatorsIncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $cache_key = 'operators_income_' . $operator->id;

        $ttl = 200;

        $operator_incomes = Cache::remember($cache_key, $ttl, function () use ($operator) {
            return operators_income::with(['payment.operator', 'payment.package'])
                ->where('operator_id', $operator->id)
                ->orderBy('id', 'desc')
		->limit(1500)
		->get();
        });

        $cache_key = 'source_operators_' . $operator->id;

        $source_operators = Cache::remember($cache_key, $ttl, function () use ($operator_incomes) {
            $source_operator_ids = $operator_incomes->pluck('source_operator_id')->unique()->filter(function ($value) {
                return $value > 0;
            });

            return operator::whereIn('id', $source_operator_ids)->get();
        });

        if ($request->filled('source_operator_id')) {
            $operator_incomes = $operator_incomes->where('source_operator_id', $request->source_operator_id);
        }

        if ($request->filled('year')) {
            $operator_incomes = $operator_incomes->where('year', $request->year);
        }

        if ($request->filled('month')) {
            $operator_incomes = $operator_incomes->where('month', $request->month);
        }

        // total_amount
        $total_amount = $operator_incomes->sum('amount');

        // length
        $length = 30;

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
                return view('admins.group_admin.operator_incomes', [
                    'operator_incomes' => $view_incomes,
                    'length' => $length,
                    'total_amount' => $total_amount,
                    'source_operators' => $source_operators,
                ]);
                break;

            case 'operator':
                return view('admins.operator.operator_incomes', [
                    'operator_incomes' => $view_incomes,
                    'length' => $length,
                    'total_amount' => $total_amount,
                    'source_operators' => $source_operators,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.operator_incomes', [
                    'operator_incomes' => $view_incomes,
                    'length' => $length,
                    'total_amount' => $total_amount,
                    'source_operators' => $source_operators,
                ]);
                break;
        }
    }
}
