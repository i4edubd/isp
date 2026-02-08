<?php

namespace App\Http\Controllers;

use App\Models\custom_field;
use App\Models\Freeradius\customer_custom_attribute;
use Illuminate\Http\Request;

class CustomFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $custom_fields = $operator->custom_fields;

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.custom-fields', [
                    'custom_fields' => $custom_fields,
                ]);
                break;

            case 'operator':
                return view('admins.operator.custom-fields', [
                    'custom_fields' => $custom_fields,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.custom-fields', [
                    'custom_fields' => $custom_fields,
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
            case 'group_admin':
                return view('admins.group_admin.custom-field-create');
                break;

            case 'operator':
                return view('admins.operator.custom-field-create');
                break;

            case 'sub_operator':
                return view('admins.sub_operator.custom-field-create');
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
            'name' => 'required',
        ]);

        $custom_field = new custom_field();
        $custom_field->operator_id = $request->user()->id;
        $custom_field->name = $request->name;
        $custom_field->save();

        return redirect()->route('custom_fields.index')->with('success', 'Field added successfully');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\custom_field  $custom_field
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, custom_field $custom_field)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.custom-field-edit', [
                    'custom_field' => $custom_field,
                ]);
                break;

            case 'operator':
                return view('admins.operator.custom-field-edit', [
                    'custom_field' => $custom_field,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.custom-field-edit', [
                    'custom_field' => $custom_field,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\custom_field  $custom_field
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, custom_field $custom_field)
    {
        if ($request->user()->id !== $custom_field->operator_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required',
        ]);

        $custom_field->name = $request->name;
        $custom_field->save();

        return redirect()->route('custom_fields.index')->with('success', 'Field updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\custom_field  $custom_field
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, custom_field $custom_field)
    {
        if ($request->user()->id !== $custom_field->operator_id) {
            abort(403);
        }
        customer_custom_attribute::where('custom_field_id', $custom_field->id)->delete();
        $custom_field->delete();
        return redirect()->route('custom_fields.index')->with('success', 'Field deleted successfully');
    }
}
