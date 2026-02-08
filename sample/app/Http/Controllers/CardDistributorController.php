<?php

namespace App\Http\Controllers;

use App\Models\card_distributor;
use App\Models\yearly_card_distributor_payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CardDistributorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $where = [
            ['operator_id', '=', $request->user()->id],
        ];

        $card_distributors = card_distributor::where($where)->get();

        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.card-distributors', [
                    'card_distributors' => $card_distributors,
                ]);
                break;

            case 'operator':
                return view('admins.operator.card-distributors', [
                    'card_distributors' => $card_distributors,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.card-distributors', [
                    'card_distributors' => $card_distributors,
                ]);
                break;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.card-distributors-create');
                break;

            case 'operator':
                return view('admins.operator.card-distributors-create');
                break;

            case 'sub_operator':
                return view('admins.sub_operator.card-distributors-create');
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $mobile = validate_mobile($request->mobile, getCountryCode($request->user()->id));

        //Invalid Mobile
        if ($mobile == 0) {
            abort(500, 'invalid mobile number');
        }

        $request->validate([
            'account_type' => 'required|in:prepaid,postpaid',
            'name' => 'required|string',
            'mobile' => 'required',
            'store_name' => 'required',
            'store_address' => 'required',
        ]);

        $card_distributor = new card_distributor();
        $card_distributor->operator_id = $request->user()->id;
        $card_distributor->country_id = $request->user()->country_id;
        $card_distributor->timezone = $request->user()->timezone;
        $card_distributor->lang_code = $request->user()->lang_code;
        $card_distributor->name = $request->name;
        $card_distributor->mobile = $request->mobile;
        $card_distributor->account_type = $request->account_type;
        $card_distributor->email = $request->email;
        $card_distributor->email_verified_at = Carbon::now(config('app.timezone'));
        $card_distributor->password = Hash::make($request->password);
        $card_distributor->store_name = $request->store_name;
        $card_distributor->store_address = $request->store_address;
        $card_distributor->save();
        return redirect()->route('card_distributors.index')->with('success', 'Card Distributor has been added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\card_distributor  $card_distributor
     * @return \Illuminate\Http\Response
     */
    public function show(card_distributor $card_distributor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\card_distributor  $card_distributor
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, card_distributor $card_distributor)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.card-distributors-edit', [
                    'card_distributor' => $card_distributor,
                ]);
                break;

            case 'operator':
                return view('admins.operator.card-distributors-edit', [
                    'card_distributor' => $card_distributor,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.card-distributors-edit', [
                    'card_distributor' => $card_distributor,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\card_distributor  $card_distributor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, card_distributor $card_distributor)
    {
        $mobile = validate_mobile($request->mobile, getCountryCode($request->user()->id));

        //Invalid Mobile
        if ($mobile == 0) {
            abort(500, 'invalid mobile number');
        }

        if ($request->user()->id !== $card_distributor->operator_id) {
            abort(500, 'not authorized');
        }

        $request->validate([
            'account_type' => 'required|in:prepaid,postpaid',
            'email' => 'email|required',
            'name' => 'required|string',
            'mobile' => 'required',
            'store_name' => 'required',
            'store_address' => 'required',
        ]);

        if ($request->email !== $card_distributor->email) {
            if (card_distributor::where('email', $request->email)->count()) {
                return redirect()->route('card_distributors.index')->with('error', 'Duplicate Email Address!');
            }
        }

        $card_distributor->country_id = $request->user()->country_id;
        $card_distributor->timezone = $request->user()->timezone;
        $card_distributor->lang_code = $request->user()->lang_code;
        $card_distributor->name = $request->name;
        $card_distributor->mobile = $request->mobile;
        $card_distributor->account_type = $request->account_type;
        $card_distributor->email = $request->email;
        $card_distributor->store_name = $request->store_name;
        $card_distributor->store_address = $request->store_address;
        if ($request->filled('password')) {
            $card_distributor->password = Hash::make($request->password);
        }
        $card_distributor->save();
        return redirect()->route('card_distributors.index')->with('success', 'Card Distributor has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\card_distributor  $card_distributor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, card_distributor $card_distributor)
    {

        if ($request->user()->id !== $card_distributor->operator_id) {
            abort(500, 'not authorized');
        }

        yearly_card_distributor_payment::where('operator_id', $card_distributor->operator_id)
            ->where('card_distributor_id', $card_distributor->id)
            ->delete();

        $card_distributor->delete();
        return redirect()->route('card_distributors.index')->with('success', 'Deleted successfully!');
    }
}
