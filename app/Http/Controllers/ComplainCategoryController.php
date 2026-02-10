<?php

namespace App\Http\Controllers;

use App\Models\complain_category;
use Illuminate\Http\Request;

class ComplainCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $requester = $request->user();

        if (!$requester) {
            abort(403);
        }

        if ($requester->role == 'manager') {
            $operator = $requester->group_admin;
        } else {
            $operator = $requester;
        }

        $complain_categories = complain_category::where('operator_id', $operator->id)->get();

        return view('complaint_management.complain_categories', [
            'complain_categories' => $complain_categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('complaint_management.complain_category-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $requester = $request->user();

        if ($requester->role == 'manager') {
            $operator = $requester->group_admin;
        } else {
            $operator = $requester;
        }

        $request->validate([
            'name' => 'required|string',
        ]);

        $category = new complain_category();
        $category->operator_id = $operator->id;
        $category->name = $request->name;
        $category->save();

        return redirect()->route('complain_categories.index')->with('success', 'Complain Category added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\complain_category  $complain_category
     * @return \Illuminate\Http\Response
     */
    public function show(complain_category $complain_category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\complain_category  $complain_category
     * @return \Illuminate\Http\Response
     */
    public function edit(complain_category $complain_category)
    {
        return view('complaint_management.complain_category-edit', [
            'complain_category' => $complain_category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\complain_category  $complain_category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, complain_category $complain_category)
    {
        $requester = $request->user();

        if ($requester->role == 'manager') {
            $operator = $requester->group_admin;
        } else {
            $operator = $requester;
        }

        if ($operator->id !== $complain_category->operator_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string',
        ]);

        $complain_category->name = $request->name;
        $complain_category->save();

        return redirect()->route('complain_categories.index')->with('success', 'Complain Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\complain_category  $complain_category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, complain_category $complain_category)
    {
        $requester = $request->user();

        if ($requester->role == 'manager') {
            $operator = $requester->group_admin;
        } else {
            $operator = $requester;
        }

        if ($operator->id !== $complain_category->operator_id) {
            abort(403);
        }

        $complain_category->delete();

        return redirect()->route('complain_categories.index')->with('success', 'Complain Category Removed successfully!');
    }
}
