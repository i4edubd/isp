<?php

namespace App\Http\Controllers;

use App\Models\country;
use App\Models\language;
use App\Models\operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class OperatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operators_where = [
            ['gid', '=', $request->user()->id],
            ['role', '=', 'operator'],
        ];

        $operators = operator::with(['accountsOwns', 'accountsProvides'])->where($operators_where)->get();

        return view('admins.group_admin.operators', [
            'operators' => $operators,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // countries
        $countries = country::all();
        $country = $countries->firstWhere('id', '=', $request->user()->country_id);
        if ($country) {
            $countries =  $countries->prepend($country);
        }

        // languages
        $languages = language::all();
        $languages = $languages->prepend(getLanguage($request->user()));

        // view
        return view('admins.group_admin.operators-create', [
            'countries' => $countries,
            'languages' => $languages,
        ]);
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
            'country_id' => 'numeric|exists:countries,id|required',
            'lang_code' => 'string|exists:languages,code|required',
            'timezone' => 'string|exists:timezones,name|required',
            'company' => 'required|string|max:254',
            'company_in_native_lang' => 'required|string|max:254',
            'name' => 'required',
            'mobile' => 'required',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:operators'],
            'password' => 'required',
            'account_type' => 'required',
        ]);

        $country = country::findOrFail($request->country_id);

        $mobile = validate_mobile($request->mobile, $country->iso2);

        $operator = new operator();
        $operator->sid = $request->user()->sid;
        $operator->mgid = $request->user()->mgid;
        $operator->gid = $request->user()->id;
        $operator->country_id = $country->id;
        $operator->timezone = $request->timezone;
        $operator->lang_code = $request->lang_code;
        $operator->name = $request->name;
        $operator->email = $request->email;
        $operator->email_verified_at = Carbon::now(config('app.timezone'));
        $operator->password = Hash::make($request->password);
        $operator->company = $request->company;
        $operator->company_in_native_lang = $request->company_in_native_lang;
        $operator->radius_db_connection = $request->user()->radius_db_connection;
        $operator->mobile = $mobile;
        $operator->helpline = $mobile;
        $operator->role = 'operator';
        $operator->provisioning_status = 2;
        $operator->account_type = $request->account_type;
        if ($request->filled('credit_limit')) {
            $operator->credit_limit = is_int($request->credit_limit) ? $request->credit_limit : 0;
        }
        $operator->save();

        // Store Cash In
        if ($request->account_type == 'debit') {
            $amount = 0;
            if ($request->filled('account_balance')) {
                $amount = $request->account_balance;
            }
            OperatorsAccountCreditController::store($operator, $amount);
        }

        return redirect()->route('operators.billing_profiles.create', ['operator' => $operator->id])->with('success', 'Please Assign Billing Profiles');

        return redirect()->route('operators.index')->with('success', 'Operator has been added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function show(operator $operator)
    {
        return view('admins.group_admin.operators-show', [
            'operator' => $operator
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
        $this->authorize('update', $operator);

        return view('admins.group_admin.operators-edit', [
            'operator' => $operator,
        ]);
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

        $this->authorize('update', $operator);

        // validate mobile
        $mobile = validate_mobile($request->mobile, getCountryCode($operator->id));

        if (!$mobile) {
            return redirect()->route('operators.index')->with('error', 'Invalid Mobile Number!');
        }

        // validate email
        $request->validate([
            'email' => 'email|required',
            'credit_limit' => 'numeric',
        ]);

        if ($request->email !== $operator->email) {
            if (operator::where('email', $request->email)->count()) {
                return redirect()->route('operators.index')->with('error', 'Duplicate Email Address!');
            }
        }

        // validate company
        $request->validate([
            'company' => 'required|string|max:254',
            'company_in_native_lang' => 'required|string|max:254',
        ]);

        // save
        $operator->company = $request->company;
        $operator->company_in_native_lang = $request->company_in_native_lang;
        $operator->name = $request->name;
        $operator->mobile = $request->mobile;
        $operator->email = $request->email;
        $operator->account_type = $request->account_type;
        if ($request->filled('password')) {
            $operator->password = Hash::make($request->password);
        }
        $operator->save();

        return redirect()->route('operators.index')->with('success', 'Operator has been edited successfully!');
    }
}
