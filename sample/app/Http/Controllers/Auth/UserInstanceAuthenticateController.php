<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserInstanceAuthenticateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'operator_id' => 'required|numeric',
        ]);

        $operator = operator::findOrFail($request->operator_id);

        $admin = $request->user();

        $authorized = 0;

        if ($admin->role == 'developer') {
            $authorized = 1;
        }

        if ($admin->id == $operator->gid) {
            $authorized = 1;
        }

        if ($authorized == 0) {
            abort(403);
        }

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        Auth::login($operator, true);

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, operator $operator)
    {
        $admin = $request->user();

        $authorized = 0;

        if ($admin->role == 'developer') {
            $authorized = 1;
        }

        if ($admin->id == $operator->gid) {
            $authorized = 1;
        }

        if ($authorized == 0) {
            abort(403);
        }

        return view('auth.authenticate-operator-instance', [
            'operator' => $operator,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function edit(operator $operator)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, operator $operator)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function destroy(operator $operator)
    {
        //
    }
}
