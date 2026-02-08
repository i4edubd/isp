<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataPolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        switch ($operator->role) {

            case 'super_admin':
                return view('admins.super_admin.data-policy');
                break;

            case 'group_admin':
                return view('admins.group_admin.data-policy');
                break;

            case 'operator':
                return view('admins.operator.data-policy');
                break;

            case 'sub_operator':
                return view('admins.sub_operator.data-policy');
                break;
        }
    }
}
