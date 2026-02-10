<?php

namespace App\Http\Controllers;

use App\Models\account;
use App\Models\operator;
use App\Models\cash_out;
use App\Models\cash_in;
use App\Models\yearly_cash_in;
use App\Models\yearly_cash_out;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AccountController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function accountsPayable(Request $request)
    {
        $accounts = account::with(['owner'])->where('account_provider', $request->user()->id)->get();

        switch ($request->user()->role) {
            case 'developer':
                return view('admins.developer.accounts-payable', [
                    'accounts' => $accounts,
                ]);
                break;

            case 'super_admin':
                return view('admins.super_admin.accounts-payable', [
                    'accounts' => $accounts,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.accounts-payable', [
                    'accounts' => $accounts,
                ]);
                break;

            case 'operator':
                return view('admins.operator.accounts-payable', [
                    'accounts' => $accounts,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.accounts-payable', [
                    'accounts' => $accounts,
                ]);
                break;
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function accountsReceivable(Request $request)
    {
        $accounts = account::with(['provider'])->where('account_owner', $request->user()->id)->get();

        switch ($request->user()->role) {
            case 'developer':
                return view('admins.developer.accounts-receivable', [
                    'accounts' => $accounts,
                ]);
                break;

            case 'super_admin':
                return view('admins.super_admin.accounts-receivable', [
                    'accounts' => $accounts,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.accounts-receivable', [
                    'accounts' => $accounts,
                ]);
                break;

            case 'operator':
                return view('admins.operator.accounts-receivable', [
                    'accounts' => $accounts,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.accounts-receivable', [
                    'accounts' => $accounts,
                ]);
                break;
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\account  $account
     * @return \Illuminate\Http\Response
     */
    public function transactions(Request $request, account $account)
    {
        $previous_url = "#";

        if ($request->user()->id == $account->account_owner) {
            $previous_url = route('accounts.receivable');
            $breadcrumb_label = 'Accounts Receivable';
            $activated_link = 1;
        }

        if ($request->user()->id == $account->account_provider) {
            $previous_url = route('accounts.payable');
            $breadcrumb_label = 'Accounts Payable';
            $activated_link = 2;
        }

        if ($request->filled('year')) {
            $year = $request->year;
            $where = [
                ['account_id', '=', $account->id],
                ['year', '=', $year],
            ];
        } else {
            $year = date(config('app.year_format'));
            $where = [
                ['account_id', '=', $account->id],
                ['year', '=', $year],
            ];
        }

        $summary_ins = yearly_cash_in::where($where)->get();

        $summary_outs = yearly_cash_out::where($where)->get();

        //transaction report
        $transaction_report = [];

        //in
        $monthly_report = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = Carbon::create(date('01-' . $i . '-Y'))->format(config('app.month_format'));
            $where[2] = ['month', '=', $month];
            $monthly_report[$month] = cash_in::select('month', 'year', DB::raw('sum(amount) as amount'))
                ->where($where)
                ->groupBy('month', 'year')
                ->get();
        }

        $transaction_report['in'] = $monthly_report;

        //out
        $monthly_report = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = Carbon::create(date('01-' . $i . '-Y'))->format(config('app.month_format'));
            $where[2] = ['month', '=', $month];
            $monthly_report[$month] = cash_out::select('month', 'year', DB::raw('sum(amount) as amount'))
                ->where($where)
                ->groupBy('month', 'year')
                ->get();
        }

        $transaction_report['out'] = $monthly_report;

        $role = $request->user()->role;

        switch ($role) {
            case 'developer':
                return view('admins.developer.accounts-transactions', [
                    'account' => $account,
                    'year' => $year,
                    'transaction_report' => $transaction_report,
                    'summary_ins' => $summary_ins,
                    'summary_outs' => $summary_outs,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'super_admin':
                return view('admins.super_admin.accounts-transactions', [
                    'account' => $account,
                    'year' => $year,
                    'transaction_report' => $transaction_report,
                    'summary_ins' => $summary_ins,
                    'summary_outs' => $summary_outs,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.accounts-transactions', [
                    'account' => $account,
                    'year' => $year,
                    'transaction_report' => $transaction_report,
                    'summary_ins' => $summary_ins,
                    'summary_outs' => $summary_outs,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'operator':
                return view('admins.operator.accounts-transactions', [
                    'account' => $account,
                    'year' => $year,
                    'transaction_report' => $transaction_report,
                    'summary_ins' => $summary_ins,
                    'summary_outs' => $summary_outs,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.accounts-transactions', [
                    'account' => $account,
                    'year' => $year,
                    'transaction_report' => $transaction_report,
                    'summary_ins' => $summary_ins,
                    'summary_outs' => $summary_outs,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\account  $account
     * @param int $year
     * @param string $month
     * @return \Illuminate\Http\Response
     */

    public function cashInDetails(Request $request, account $account, int $year, string $month)
    {
        if ($request->user()->id == $account->account_owner) {
            $previous_url = route('accounts.receivable');
            $breadcrumb_label = 'Accounts Receivable';
            $activated_link = 1;
        }

        if ($request->user()->id == $account->account_provider) {
            $previous_url = route('accounts.payable');
            $breadcrumb_label = 'Accounts Payable';
            $activated_link = 2;
        }

        $where = [
            ['account_id', '=', $account->id],
            ['year', '=', $year],
            ['month', '=', $month],
        ];

        $account_ins = cash_in::where($where)->get();

        $role = $request->user()->role;

        switch ($role) {
            case 'developer':
                return view('admins.developer.account-in-details', [
                    'account_ins' => $account_ins,
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'super_admin':
                return view('admins.super_admin.account-in-details', [
                    'account_ins' => $account_ins,
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.account-in-details', [
                    'account_ins' => $account_ins,
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'operator':
                return view('admins.operator.account-in-details', [
                    'account_ins' => $account_ins,
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.account-in-details', [
                    'account_ins' => $account_ins,
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\account  $account
     * @param int $year
     * @param string $month
     * @return \Illuminate\Http\Response
     */
    public function cashOutDetails(Request $request, account $account, int $year, string $month)
    {
        if ($request->user()->id == $account->account_owner) {
            $previous_url = route('accounts.receivable');
            $breadcrumb_label = 'Accounts Receivable';
            $activated_link = 1;
        }

        if ($request->user()->id == $account->account_provider) {
            $previous_url = route('accounts.payable');
            $breadcrumb_label = 'Accounts Payable';
            $activated_link = 2;
        }

        $where = [
            ['account_id', '=', $account->id],
            ['year', '=', $year],
            ['month', '=', $month],
        ];

        $account_outs = cash_out::where($where)->get();

        $role = $request->user()->role;

        switch ($role) {
            case 'developer':
                return view('admins.developer.account-out-details', [
                    'account_outs' => $account_outs,
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'super_admin':
                return view('admins.super_admin.account-out-details', [
                    'account_outs' => $account_outs,
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.account-out-details', [
                    'account_outs' => $account_outs,
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'operator':
                return view('admins.operator.account-out-details', [
                    'account_outs' => $account_outs,
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.account-out-details', [
                    'account_outs' => $account_outs,
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;
        }
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Models\operator  $account_provider
     * @param  \App\Models\operator  $account_owner
     * @return \Illuminate\Http\Response
     */
    public function store(operator $account_provider, operator $account_owner)
    {
        $where = [
            ['account_provider', '=', $account_provider->id],
            ['account_owner', '=', $account_owner->id],
        ];

        if (account::where($where)->doesntExist()) {
            $account = new account();
            $account->account_provider = $account_provider->id;
            $account->account_owner = $account_owner->id;
            $account->save();
        }

        return 1;
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, account $account)
    {
        if ($request->user()->id == $account->account_owner) {
            $previous_url = route('accounts.receivable');
            $breadcrumb_label = 'Accounts Receivable';
            $activated_link = 1;
        }

        if ($request->user()->id == $account->account_provider) {
            $previous_url = route('accounts.payable');
            $breadcrumb_label = 'Accounts Payable';
            $activated_link = 2;
        }

        $role = $request->user()->role;

        switch ($role) {
            case 'developer':
                return view('admins.developer.account-edit', [
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'super_admin':
                return view('admins.super_admin.account-edit', [
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.account-edit', [
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'operator':
                return view('admins.operator.account-edit', [
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.account-edit', [
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, account $account)
    {
        //validate
        $request->validate([
            'cash_out_instruction' => ['required', 'string', 'max:255'],
        ]);
        $account->cash_out_instruction = $request->cash_out_instruction;
        $account->save();
        return redirect()->route('accounts.payable')->with('success', 'Account Updated successfully!');
    }
}
