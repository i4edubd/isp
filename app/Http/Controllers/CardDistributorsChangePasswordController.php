<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CardDistributorsChangePasswordController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admins.card_distributors.change-password');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $card_distributor = $request->user('card');

        $request->validate([
            'password' => 'required|confirmed|min:8',
        ]);

        $card_distributor->password = Hash::make($request->password);
        $card_distributor->save();

        return redirect()->route('card.dashboard')->with('info', 'Password updated successfully!');
    }
}
