<?php

namespace App\Http\Controllers;

use App\Models\department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
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

        $departments = department::where('operator_id', $operator->id)->get();

        return view('complaint_management.departments', [
            'departments' => $departments,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('complaint_management.department-create');
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

        $department = new department();
        $department->operator_id = $operator->id;
        $department->name = $request->name;
        $department->save();

        return redirect()->route('departments.index')->with('success', 'Department added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\department  $department
     * @return \Illuminate\Http\Response
     */
    public function show(department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(department $department)
    {
        return view('complaint_management.department-edit', [
            'department' => $department,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, department $department)
    {
        $requester = $request->user();

        if ($requester->role == 'manager') {
            $operator = $requester->group_admin;
        } else {
            $operator = $requester;
        }

        if ($operator->id !== $department->operator_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string',
        ]);

        $department->name = $request->name;
        $department->save();

        return redirect()->route('departments.index')->with('success', 'Department updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, department $department)
    {
        $requester = $request->user();

        if ($requester->role == 'manager') {
            $operator = $requester->group_admin;
        } else {
            $operator = $requester;
        }

        if ($operator->id !== $department->operator_id) {
            abort(403);
        }

        $department->delete();

        return redirect()->route('departments.index')->with('success', 'Department Removed successfully!');
    }
}
