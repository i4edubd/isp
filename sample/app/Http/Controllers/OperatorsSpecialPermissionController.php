<?php

namespace App\Http\Controllers;

use App\Models\operator;
use App\Models\operator_permission;
use Illuminate\Http\Request;

class OperatorsSpecialPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function index(operator $operator)
    {
        return view('admins.group_admin.operator-special-permissions', [
            'operator' => $operator,
            'permissions' => $operator->permissions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function create(operator $operator)
    {
        $this->authorize('assignSpecialPermission', $operator);

        $all_permissions = collect(config('special_permissions'));

        $selected_permissions = $operator->permissions;

        $new_permissions = $all_permissions->diff($selected_permissions);

        return view('admins.group_admin.operator-special-permission-create', [
            'operator' => $operator,
            'selected_permissions' => $selected_permissions,
            'new_permissions' => $new_permissions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, operator $operator)
    {
        $this->authorize('assignSpecialPermission', $operator);

        operator_permission::where('operator_id', $operator->id)->delete();

        if ($request->filled('permissions')) {
            //permissions
            foreach ($request->permissions as $permission) {
                $operator_permission = new operator_permission();
                $operator_permission->operator_id = $operator->id;
                $operator_permission->permission = $permission;
                $operator_permission->save();
            }
        }

        return redirect()->route('operators.index')->with('success', 'Authorization remembered!');
    }
}
