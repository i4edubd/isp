<?php

namespace App\Http\Controllers;

use App\Models\account;
use Illuminate\Http\Request;

class AccountsMonthlyReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'year' => 'numeric|nullable',
            'month' => 'string|nullable',
        ]);

        if ($request->filled('year')) {
            $year = $request->year;
        } else {
            $year = date(config('app.year_format'));
        }

        if ($request->filled('month')) {
            $month = $request->month;
        } else {
            $month = date(config('app.month_format'));
        }

        $operator = $request->user();

        $accounts = account::with(['provider', 'owner'])->where('account_provider', $operator->id)->orWhere('account_owner', $operator->id)->get();
        $accounts_receivable = $accounts->where('account_owner', $operator->id);
        $accounts_payable = $accounts->where('account_provider', $operator->id);
        $accounts = null;

        // receivable_collection
        $receivable_collection = [];
        foreach ($accounts_receivable as $account) {
            $row = [];
            $row['type'] = 'accounts_receivable';
            $row['account_id'] = $account->id;
            $row['account_owner'] = $account->owner->name . '::' . $account->owner->readableRole;
            $row['account_provider'] = $account->provider->name . '::' . $account->provider->readableRole;
            $row['year'] = $year;
            $row['month'] = $month;
            $row['cash_in'] = $account->cash_ins()->where('year', $year)->where('month', $month)->sum('amount');
            $row['cash_outs'] = $account->cash_outs()->where('year', $year)->where('month', $month)->sum('amount');
            $row['balance'] = $account->balance;
            $receivable_collection[] = collect($row);
        }
        $accounts_receivable = null;

        // payable_collection
        $payable_collection = [];
        foreach ($accounts_payable as $account) {
            $row = [];
            $row['type'] = 'accounts_payable';
            $row['account_id'] = $account->id;
            $row['account_owner'] = $account->owner->name . '::' . $account->owner->readableRole;
            $row['account_provider'] = $account->provider->name . '::' . $account->provider->readableRole;
            $row['year'] = $year;
            $row['month'] = $month;
            $row['cash_in'] = $account->cash_ins()->where('year', $year)->where('month', $month)->sum('amount');
            $row['cash_outs'] = $account->cash_outs()->where('year', $year)->where('month', $month)->sum('amount');
            $row['balance'] = $account->balance;
            $payable_collection[] = collect($row);
        }
        $accounts_payable = null;

        $receivable_collection = collect($receivable_collection);
        $payable_collection = collect($payable_collection);
        $role = $operator->role;

        switch ($role) {
            case 'developer':
                return view('admins.developer.accounts-monthly-report', [
                    'receivable_collection' => $receivable_collection,
                    'payable_collection' => $payable_collection,
                ]);
                break;

            case 'super_admin':
                return view('admins.super_admin.accounts-monthly-report', [
                    'receivable_collection' => $receivable_collection,
                    'payable_collection' => $payable_collection,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.accounts-monthly-report', [
                    'receivable_collection' => $receivable_collection,
                    'payable_collection' => $payable_collection,
                ]);
                break;

            case 'operator':
                return view('admins.operator.accounts-monthly-report', [
                    'receivable_collection' => $receivable_collection,
                    'payable_collection' => $payable_collection,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.accounts-monthly-report', [
                    'receivable_collection' => $receivable_collection,
                    'payable_collection' => $payable_collection,
                ]);
                break;
        }
    }
}
