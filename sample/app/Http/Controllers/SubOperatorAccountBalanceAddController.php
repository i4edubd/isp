<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Sms\SmsGatewayController;
use App\Models\operator;
use Illuminate\Http\Request;

class SubOperatorAccountBalanceAddController extends Controller
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

        if ($operator->account_type !== 'debit') {
            abort(403);
        }

        return view('admins.operator.sub_operator-account-balance', [
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

        if ($operator->account_type !== 'debit') {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric',
        ]);

        OperatorsAccountCreditController::store($operator, $request->amount);

        // send SMS
        $message =  SmsGenerator::balanceAddedMsg($request->user(), $request->amount);

        $controller = new SmsGatewayController();
        $controller->sendSms($request->user(), $operator->mobile, $message, 0);

        return redirect()->route('sub_operators.index')->with('success', 'Balance has been added successfully!');
    }
}
