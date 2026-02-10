<?php

namespace App\Http\Controllers;

use App\Models\operator;
use App\Models\operator_permission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $filter = [
            ['gid', '=', $operator->id],
            ['role', '=', 'manager'],
        ];

        $managers = operator::where($filter)->get();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.managers', [
                    'managers' => $managers,
                ]);
                break;

            case 'operator':
                return view('admins.operator.managers', [
                    'managers' => $managers,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.managers', [
                    'managers' => $managers,
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

        $permissions = config('operators_permissions');

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.managers-create', [
                    'permissions' => $permissions,
                ]);
                break;

            case 'operator':
                return view('admins.operator.managers-create', [
                    'permissions' => $permissions,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.managers-create', [
                    'permissions' => $permissions,
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
        $mobile = validate_mobile($request->mobile);

        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:operators'],
            'password' => 'required',
        ]);

        $operator = new operator();
        $operator->sid = $request->user()->sid;
        $operator->mgid = $request->user()->mgid;
        $operator->gid = $request->user()->id;
        $operator->country_id = $request->user()->country_id;
        $operator->timezone = $request->user()->timezone;
        $operator->lang_code = $request->user()->lang_code;
        $operator->name = $request->name;
        $operator->email = $request->email;
        $operator->email_verified_at = Carbon::now(config('app.timezone'));
        $operator->password = Hash::make($request->password);
        $operator->company = $request->user()->company;
        $operator->radius_db_connection = $request->user()->radius_db_connection;
        $operator->mobile = $mobile;
        $operator->helpline = $mobile;
        $operator->role = 'manager';
        $operator->provisioning_status = 2;
        $operator->save();

        if ($request->filled('permissions')) {
            //permissions
            foreach ($request->permissions as $permission) {
                $operator_permission = new operator_permission();
                $operator_permission->operator_id = $operator->id;
                $operator_permission->permission = $permission;
                $operator_permission->save();
            }
        }

        return redirect()->route('managers.index')->with('success', 'Manager has been added successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\operator  $manager
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, operator $manager)
    {
        $requester = $request->user();

        if ($requester->id !== $manager->gid) {
            abort(403);
        }

        $all_permissions = collect(config('operators_permissions'));

        $selected_permissions = $manager->permissions;

        $new_permissions = $all_permissions->diff($selected_permissions);

        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.managers-edit', [
                    'manager' => $manager,
                    'selected_permissions' => $selected_permissions,
                    'new_permissions' => $new_permissions,
                ]);
                break;

            case 'operator':
                return view('admins.operator.managers-edit', [
                    'manager' => $manager,
                    'selected_permissions' => $selected_permissions,
                    'new_permissions' => $new_permissions,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.managers-edit', [
                    'manager' => $manager,
                    'selected_permissions' => $selected_permissions,
                    'new_permissions' => $new_permissions,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\operator  $manager
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, operator $manager)
    {
        $requester = $request->user();

        if ($requester->id !== $manager->gid) {
            abort(403);
        }

        $mobile = validate_mobile($request->mobile);

        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
        ]);

        $manager->country_id = $request->user()->country_id;
        $manager->timezone = $request->user()->timezone;
        $manager->lang_code = $request->user()->lang_code;
        $manager->name = $request->name;
        $manager->mobile = $mobile;
        if ($request->filled('password')) {
            $manager->password = Hash::make($request->password);
        }
        $manager->save();

        operator_permission::where('operator_id', $manager->id)->delete();

        if ($request->filled('permissions')) {
            //permissions
            foreach ($request->permissions as $permission) {
                $operator_permission = new operator_permission();
                $operator_permission->operator_id = $manager->id;
                $operator_permission->permission = $permission;
                $operator_permission->save();
            }
        }

        return redirect()->route('managers.index')->with('success', 'Manager has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\operator  $manager
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, operator $manager)
    {
        $requester = $request->user();

        if ($requester->id !== $manager->gid) {
            abort(403);
        }

        $manager->delete();
        return redirect()->route('managers.index')->with('success', 'Manager has been deleted successfully!');
    }
}
