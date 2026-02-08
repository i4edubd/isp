<?php

namespace App\Http\Controllers;

use App\Models\pending_transaction;
use App\Models\account;
use Illuminate\Http\Request;

class PendingTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $pending_transactions = pending_transaction::with(['sender', 'receiver'])
            ->where('account_provider', $operator->id)
            ->orWhere('account_owner', $operator->id)
            ->get();

        switch ($operator->role) {
            case 'developer':
                return view('admins.developer.pending-transactions', [
                    'pending_transactions' => $pending_transactions,
                ]);
                break;

            case 'super_admin':
                return view('admins.super_admin.pending-transactions', [
                    'pending_transactions' => $pending_transactions,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.pending-transactions', [
                    'pending_transactions' => $pending_transactions,
                ]);
                break;

            case 'operator':
                return view('admins.operator.pending-transactions', [
                    'pending_transactions' => $pending_transactions,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.pending-transactions', [
                    'pending_transactions' => $pending_transactions,
                ]);
                break;
        }
    }

    /**
     * Show the form for creating cash out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\account  $account
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, account $account)
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

        $count_pending = pending_transaction::where('account_id', $account->id)->count();

        if ($count_pending) {
            return redirect()->route('pending_transactions.index');
        }

        $role = $request->user()->role;

        switch ($role) {
            case 'developer':
                return view('admins.developer.accounts-cash-out', [
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'super_admin':
                return view('admins.super_admin.accounts-cash-out', [
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.accounts-cash-out', [
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'operator':
                return view('admins.operator.accounts-cash-out', [
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.accounts-cash-out', [
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, account $account)
    {
        $request->validate([
            'amount' => 'required|numeric'
        ]);

        $pending_transaction = new pending_transaction();
        $pending_transaction->account_id = $account->id;
        $pending_transaction->account_provider = $account->account_provider;
        $pending_transaction->account_owner = $account->account_owner;
        $pending_transaction->amount = $request->amount;
        $pending_transaction->date = date(config('app.date_format'));
        $pending_transaction->note = $request->note;
        $pending_transaction->save();

        return redirect()->route('pending_transactions.index')->with('success', 'Request will be validated by Receiver');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\pending_transaction  $pending_transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(pending_transaction $pending_transaction)
    {
        $pending_transaction->delete();
        return redirect()->route('pending_transactions.index')->with('error', 'Transaction canceled');
    }
}
