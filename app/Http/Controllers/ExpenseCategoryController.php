<?php

namespace App\Http\Controllers;

use App\Models\expense_category;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $where = [
            ['operator_id', '=', $operator->id],
            ['hidden', '=', 'no'],
        ];

        $expense_categories = expense_category::with('subcategories')->where($where)->get();

        switch ($operator->role) {
            case 'super_admin':
                return view('admins.super_admin.expense-categories', [
                    'expense_categories' => $expense_categories,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.expense-categories', [
                    'expense_categories' => $expense_categories,
                ]);
                break;

            case 'operator':
                return view('admins.operator.expense-categories', [
                    'expense_categories' => $expense_categories,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.expense-categories', [
                    'expense_categories' => $expense_categories,
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

        $operator = $request->user();

        switch ($operator->role) {
            case 'super_admin':
                return view('admins.super_admin.expense-category-create');
                break;

            case 'group_admin':
                return view('admins.group_admin.expense-category-create');
                break;

            case 'operator':
                return view('admins.operator.expense-category-create');
                break;

            case 'sub_operator':
                return view('admins.sub_operator.expense-category-create');
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
            'category_name' => 'required|string',
        ]);
        $expense_category = new expense_category();
        $expense_category->operator_id = $request->user()->id;
        $expense_category->category_name = $request->category_name;
        $expense_category->save();
        return redirect()->route('expense_categories.index')->with('success', 'Expense Category Created Successfully');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\expense_category  $expense_category
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, expense_category $expense_category)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'super_admin':
                return view('admins.super_admin.expense-category-edit', [
                    'expense_category' => $expense_category,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.expense-category-edit', [
                    'expense_category' => $expense_category,
                ]);
                break;

            case 'operator':
                return view('admins.operator.expense-category-edit', [
                    'expense_category' => $expense_category,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.expense-category-edit', [
                    'expense_category' => $expense_category,
                ]);
                break;
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\expense_category  $expense_category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, expense_category $expense_category)
    {
        $request->validate([
            'category_name' => 'required|string',
        ]);
        $expense_category->category_name = $request->category_name;
        $expense_category->save();
        return redirect()->route('expense_categories.index')->with('success', 'Expense Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\expense_category  $expense_category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, expense_category $expense_category)
    {
        if ($request->user()->id !== $expense_category->operator_id) {
            abort(403);
        }
        $expense_category->hidden = 'yes';
        $expense_category->save();
        return redirect()->route('expense_categories.index')->with('success', 'successfully deleted!');
    }
}
