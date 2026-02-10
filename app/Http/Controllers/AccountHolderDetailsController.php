<?php

namespace App\Http\Controllers;

use App\Models\operator;
use Illuminate\Http\Request;

class AccountHolderDetailsController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function show(operator $operator)
    {
        return view('admins.components.account-holder-details', [
            'operator' => $operator,
        ]);
    }
}
