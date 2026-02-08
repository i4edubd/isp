<?php

namespace App\Http\Controllers;

use App\Models\expense;
use App\Models\expense_category;
use App\Models\yearly_expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Spatie\SimpleExcel\SimpleExcelWriter;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Expense::class);

        $operator = $request->user();

        if ($operator->role === 'manager') {
            $where[0] = ['operator_id', '=', $operator->group_admin->id];
        } else {
            $where[0] = ['operator_id', '=', $operator->id];
        }

        if ($request->filled('expense_date')) {
            $where[1] = ['expense_date', '=', $request->expense_date];
        } else {
            $where[1] = ['expense_date', '=', date(config('app.date_format'))];
        }

        $expenses = expense::with(['category', 'subcategory'])->where($where)->get();

        switch ($operator->role) {
            case 'super_admin':
                return view('admins.super_admin.expenses', [
                    'expenses' => $expenses,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.expenses', [
                    'expenses' => $expenses,
                ]);
                break;

            case 'operator':
                return view('admins.operator.expenses', [
                    'expenses' => $expenses,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.expenses', [
                    'expenses' => $expenses,
                ]);
                break;

            case 'manager':
                return view('admins.manager.expenses', [
                    'expenses' => $expenses,
                ]);
                break;
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Expense::class);

        $operator = $request->user();

        if ($operator->role === 'manager') {
            $where = [
                ['operator_id', '=', $operator->group_admin->id],
                ['hidden', '=', 'no'],
            ];
        } else {
            $where = [
                ['operator_id', '=', $operator->id],
                ['hidden', '=', 'no'],
            ];
        }

        $expense_categories = expense_category::where($where)->get();

        switch ($operator->role) {

            case 'super_admin':
                return view('admins.super_admin.expenses-create', [
                    'expense_categories' => $expense_categories,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.expenses-create', [
                    'expense_categories' => $expense_categories,
                ]);
                break;

            case 'operator':
                return view('admins.operator.expenses-create', [
                    'expense_categories' => $expense_categories,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.expenses-create', [
                    'expense_categories' => $expense_categories,
                ]);
                break;

            case 'manager':
                return view('admins.manager.expenses-create', [
                    'expense_categories' => $expense_categories,
                ]);
                break;
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
        $request->validate([
            'amount' => 'required|numeric',
            'expense_category_id' => 'required|numeric',
            'expense_date' => 'required|string',
        ]);

        $requester = $request->user();

        if ($requester->role === 'manager') {
            $operator_id = $requester->group_admin->id;
        } else {
            $operator_id = $requester->id;
        }

        $expense = new expense();
        $expense->operator_id = $operator_id;
        $expense->expense_category_id = $request->expense_category_id;
        $expense->expense_subcategory_id = $request->expense_subcategory_id;
        $expense->amount = $request->amount;
        $expense->note = $request->note;
        $expense->expense_date = date_format(date_create($request->expense_date), config('app.date_format'));
        $expense->week = date_format(date_create($request->expense_date), config('app.week_format'));
        $expense->month = date_format(date_create($request->expense_date), config('app.month_format'));
        $expense->year = date_format(date_create($request->expense_date), config('app.year_format'));
        $expense->save();

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, expense $expense)
    {
        $this->authorize('update', $expense);

        $operator = $request->user();

        if ($operator->role === 'manager') {
            $where = [
                ['operator_id', '=', $operator->group_admin->id],
                ['hidden', '=', 'no'],
            ];
        } else {
            $where = [
                ['operator_id', '=', $operator->id],
                ['hidden', '=', 'no'],
            ];
        }

        $expense_categories = expense_category::where($where)->get();

        switch ($operator->role) {

            case 'super_admin':
                return view('admins.super_admin.expenses-edit', [
                    'expense_categories' => $expense_categories,
                    'expense' => $expense,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.expenses-edit', [
                    'expense_categories' => $expense_categories,
                    'expense' => $expense,
                ]);
                break;

            case 'operator':
                return view('admins.operator.expenses-edit', [
                    'expense_categories' => $expense_categories,
                    'expense' => $expense,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.expenses-edit', [
                    'expense_categories' => $expense_categories,
                    'expense' => $expense,
                ]);
                break;

            case 'manager':
                return view('admins.manager.expenses-edit', [
                    'expense_categories' => $expense_categories,
                    'expense' => $expense,
                ]);
                break;
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, expense $expense)
    {
        $this->authorize('update', $expense);

        $request->validate([
            'amount' => 'required|numeric',
            'expense_category_id' => 'required|numeric',
            'expense_date' => 'required|string',
        ]);

        $expense->expense_category_id = $request->expense_category_id;
        $expense->expense_subcategory_id = $request->expense_subcategory_id;
        $expense->amount = $request->amount;
        $expense->note = $request->note;
        $expense->expense_date = date_format(date_create($request->expense_date), config('app.date_format'));
        $expense->save();
        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, expense $expense)
    {
        if ($request->user()->id !== $expense->operator_id) {
            abort(403);
        }
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense Deleted successfully');
    }


    /**
     * Show Expense Report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function expenseReport(Request $request)
    {
        $operator = $request->user();

        if ($request->year) {
            $year = $request->year;
            $where = [
                ['operator_id', '=', $request->user()->id],
                ['year', '=', $year],
            ];
        } else {
            $year = date(config('app.year_format'));
            $where = [
                ['operator_id', '=', $request->user()->id],
                ['year', '=', $year]
            ];
        }

        // summary_report
        $summary_report = yearly_expense::where($where)->get();

        // expenses
        $expenses = expense::with(['category', 'subcategory'])->where($where)->get();

        switch ($operator->role) {

            case 'super_admin':
                return view('admins.super_admin.expense-report', [
                    'year' => $year,
                    'summary_report' => $summary_report,
                    'expenses' => $expenses,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.expense-report', [
                    'year' => $year,
                    'summary_report' => $summary_report,
                    'expenses' => $expenses,
                ]);
                break;

            case 'operator':
                return view('admins.operator.expense-report', [
                    'year' => $year,
                    'summary_report' => $summary_report,
                    'expenses' => $expenses,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.expense-report', [
                    'year' => $year,
                    'summary_report' => $summary_report,
                    'expenses' => $expenses,
                ]);
                break;
        }
    }



    /**
     * Show Expense Report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function expenseDetails(Request $request)
    {
        $request->validate([
            'year' => 'required',
            'expense_category_id' => 'required',
        ]);

        $year = $request->year;

        $where = [
            ['operator_id', '=', $request->user()->id],
            ['expense_category_id', '=', $request->expense_category_id],
            ['year', '=', $year]
        ];

        if ($request->month) {
            $where[3] = ['month', '=', $request->month];
        }

        return view('admins.components.expense-details', [
            'expenses' => expense::with(['category', 'subcategory'])->where($where)->get()
        ]);
    }

    /**
     * Download Expense Report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function downloadExpenseReport(Request $request)
    {
        $operator = $request->user();

        $where = [];

        $where[] = ['operator_id', '=', $operator->id];

        if ($request->filled('year')) {
            $where[] = ['year', '=', $request->year];
        }

        if ($request->filled('month')) {
            $where[] = ['month', '=', $request->month];
        }

        $expenses = expense::with(['category', 'subcategory'])->where($where)->get();

        $writer = SimpleExcelWriter::streamDownload('expenses.xlsx');

        foreach ($expenses as $expense) {
            $writer->addRow([
                "Year" => $expense->year,
                "Month" => $expense->month,
                "Date" => $expense->expense_date,
                "Category" => $expense->category->category_name,
                "Sub Category" => $expense->subcategory->expense_subcategory_name,
                "Amount" => $expense->amount,
                "Note" => $expense->note,
            ]);
        }

        $total = expense::where($where)->sum('amount');

        $writer->addRow([
            "Year" => "",
            "Month" => "",
            "Date" => "",
            "Category" => "",
            "Sub Category" => "Total",
            "Amount" => $total,
            "Note" => "",
        ]);

        $writer->toBrowser();
    }
}
