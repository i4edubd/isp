<?php

namespace App\Http\Controllers;

use App\Models\operator;
use Illuminate\Http\Request;

class FormController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function selectOperator(Request $request)
    {

        $group_admin = $request->user();

        if ($group_admin->role !== 'group_admin') {
            return "Has No Operator";
        }

        $where = [
            ['gid', '=', $group_admin->id],
            ['role', '=', 'operator'],
        ];

        $operators = operator::where($where)->get();
        $operators = $operators->push($request->user());

        return view('admins.components.select-operator', [
            'operators' => $operators,
        ]);
    }


    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function optionsforAccountType(Request $request)
    {
        $request->validate([
            'account_type' => 'required|in:credit,debit',
        ]);

        if ($request->account_type == 'credit') {
            return view('admins.components.credit-account-option');
        }

        if ($request->account_type == 'debit') {
            return view('admins.components.debit-account-option');
        }
    }
}
