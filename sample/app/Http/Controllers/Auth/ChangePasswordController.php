<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{

    /**
     * Show the password Reset Form
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'super_admin':
                return view('admins.super_admin.change-password');
                break;

            case 'group_admin':
                return view('admins.group_admin.change-password');
                break;

            case 'operator':
                return view('admins.operator.change-password');
                break;

            case 'sub_operator':
                return view('admins.sub_operator.change-password');
                break;

            case 'manager':
                return view('admins.manager.change-password');
                break;

            case 'developer':
                return view('admins.developer.change-password');
                break;

            case 'sales_manager':
                return view('admins.sales_manager.change-password');
                break;
        }
    }


    /**
     * Store the Updated Password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)

    {
        $operator = $request->user();

        $request->validate([
            'password' => 'required|confirmed|min:8',
        ]);

        $operator->password = Hash::make($request->password);
        $operator->save();

        return redirect()->route('admin.dashboard')->with('success', 'Password updated successfully!');
    }
}
