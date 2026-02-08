<?php

namespace App\Http\Controllers;

use App\Models\authentication_log;
use Illuminate\Http\Request;

class AuthenticationLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $authentication_logs = authentication_log::where('operator_id', $operator->id)->get();

        return match ($operator->role) {
            'group_admin' => view('admins.group_admin.authentication_logs', ['authentication_logs' => $authentication_logs]),
            'operator' => view('admins.operator.authentication_logs', ['authentication_logs' => $authentication_logs]),
            'sub_operator' => view('admins.sub_operator.authentication_logs', ['authentication_logs' => $authentication_logs]),
            'manager' => view('admins.manager.authentication_logs', ['authentication_logs' => $authentication_logs]),
        };
    }
}
