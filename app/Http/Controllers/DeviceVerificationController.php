<?php

namespace App\Http\Controllers;

use App\Models\operator;
use Illuminate\Http\Request;

class DeviceVerificationController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function create(operator $operator)
    {
        return view('admins.components.operator-otp-verification', [
            'operator' => $operator,
            'action' => route('operators.device-verification.store', ['operator' => $operator]),
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
        $request->validate([
            'code' => 'required|numeric',
        ]);

        if ($operator->two_factor_recovery_codes != $request->code) {
            return redirect()->route('operators.device-verification.create', ['operator' => $operator])->with('info', 'Invalid Code!');
        }

        $operator->device_identification_pending = 0;
        $operator->save();

        return redirect()->route('dashboard');
    }
}
