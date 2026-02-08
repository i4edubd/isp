<?php

namespace App\Http\Controllers;

use App\Models\expense_subcategory;
use App\Models\expense_category;
use Illuminate\Http\Request;

class ExpenseSubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(expense_category $expense_category)
    {
        $expense_subcategories = $expense_category->subcategories;
        return view('admins.components.expense-subcategory-options', [
            'expense_subcategories' => $expense_subcategories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, expense_category $expense_category)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'super_admin':
                return view('admins.super_admin.expense-subcategory-create', [
                    'expense_category' => $expense_category,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.expense-subcategory-create', [
                    'expense_category' => $expense_category,
                ]);
                break;

            case 'operator':
                return view('admins.operator.expense-subcategory-create', [
                    'expense_category' => $expense_category,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.expense-subcategory-create', [
                    'expense_category' => $expense_category,
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
    public function store(Request $request, expense_category $expense_category)
    {
        $request->validate([
            'expense_subcategory_name' => 'required|string'
        ]);

        $expense_subcategory = new expense_subcategory();
        $expense_subcategory->operator_id = $request->user()->id;
        $expense_subcategory->expense_category_id = $expense_category->id;
        $expense_subcategory->expense_subcategory_name = $request->expense_subcategory_name;
        $expense_subcategory->save();

        return redirect()->route('expense_categories.index')->with('success', 'Expense Sub Category created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\expense_subcategory  $expense_subcategory
     * @return \Illuminate\Http\Response
     */
    public function show(expense_subcategory $expense_subcategory)
    {
        return view('admins.components.expense-subcategory-show', [
            'expense_subcategory' => $expense_subcategory,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\expense_subcategory  $expense_subcategory
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, expense_subcategory $expense_subcategory)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'super_admin':
                return view('admins.super_admin.expense-subcategory-edit', [
                    'expense_subcategory' => $expense_subcategory,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.expense-subcategory-edit', [
                    'expense_subcategory' => $expense_subcategory,
                ]);
                break;

            case 'operator':
                return view('admins.operator.expense-subcategory-edit', [
                    'expense_subcategory' => $expense_subcategory,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.expense-subcategory-edit', [
                    'expense_subcategory' => $expense_subcategory,
                ]);
                break;
        }
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\expense_subcategory  $expense_subcategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, expense_subcategory $expense_subcategory)
    {
        $request->validate([
            'expense_subcategory_name' => 'required|string',
        ]);
        $expense_subcategory->expense_subcategory_name = $request->expense_subcategory_name;
        $expense_subcategory->save();
        return redirect()->route('expense_categories.index')->with('success', 'Sub Category updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\expense_subcategory  $expense_subcategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, expense_subcategory $expense_subcategory)
    {
        if ($request->user()->id !== $expense_subcategory->operator_id) {
            abort(403);
        }
        $expense_subcategory->hidden = 'yes';
        $expense_subcategory->save();
        return redirect()->route('expense_categories.index')->with('success', 'Sub Category deleted successfully');
    }
}
