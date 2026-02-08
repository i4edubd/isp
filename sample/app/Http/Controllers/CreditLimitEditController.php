<?php

namespace App\Http\Controllers;

use App\Models\operator;
use Illuminate\Http\Request;

class CreditLimitEditController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, operator $operator)
    {
        if ($request->user()->id !== $operator->gid) {
            abort(403);
        }

        if ($operator->account_type !== 'credit') {
            abort(403);
        }

        return view('admins.group_admin.operator-credit-limit', [
            'operator' => $operator,
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
        if ($request->user()->id !== $operator->gid) {
            abort(403);
        }

        if ($operator->account_type !== 'credit') {
            abort(403);
        }

        $request->validate([
            'credit_limit' => 'required|numeric',
        ]);

        $operator->credit_limit = $request->credit_limit;
        $operator->save();

        return redirect()->route('operators.index')->with('success', 'Credit Limit Edited successfully!');
    }
}
