<?php

namespace App\Http\Controllers;

use App\Models\operator;
use Illuminate\Http\Request;

class SelfProvisioningController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, operator $operator)
    {
        if ($request->filled('sp_key')) {
            if ($request->sp_key == $operator->sp_key) {
                $operator->sp_request = 1;
                $operator->save();
                return 'Request accepted with thanks!';
            } else {
                abort(403);
            }
        } else {
            abort(403);
        }
    }
}
