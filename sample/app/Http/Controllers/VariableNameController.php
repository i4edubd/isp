<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VariableNameController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            "string" => "required|string",
        ]);

        return getVarName($request->string);
    }
}
