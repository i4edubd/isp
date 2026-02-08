<?php

namespace App\Http\Controllers;

use App\Models\account;
use App\Models\cash_in;
use App\Models\cash_out;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;

class AccountStatementController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\account  $account
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

        $this->authorize('view', $account);

        $role = $request->user()->role;

        switch ($role) {
            case 'super_admin':
                return view('admins.super_admin.account-statement', [
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.account-statement', [
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'operator':
                return view('admins.operator.account-statement', [
                    'account' => $account,
                    'previous_url' => $previous_url,
                    'breadcrumb_label' => $breadcrumb_label,
                    'activated_link' => $activated_link,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.account-statement', [
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
     * @param  \App\Models\account  $account
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, account $account)
    {
        $this->authorize('view', $account);

        $request->validate([
            'transaction_type' => 'required|in:cash_in,cash_out,all',
            'from_date' => 'required|string',
            'to_date' => 'required|string',
        ]);

        $from_date = date_format(date_create($request->from_date), config('app.date_format'));
        $to_date = date_format(date_create($request->to_date), config('app.date_format'));
        $created_from = Carbon::createFromFormat(config('app.date_format'), $from_date)->startOfDay();
        $created_to = Carbon::createFromFormat(config('app.date_format'), $to_date)->endOfDay();

        $transaction_type = $request->transaction_type;

        switch ($transaction_type) {
            case 'cash_in':
                $statements = cash_in::where('account_id', $account->id)
                    ->whereBetween('created_at', [$created_from, $created_to])
                    ->get();
                break;

            case 'cash_out':
                $statements = cash_out::where('account_id', $account->id)
                    ->whereBetween('created_at', [$created_from, $created_to])
                    ->get();
                break;

            case 'all':
                $cash_ins = cash_in::where('account_id', $account->id)
                    ->whereBetween('created_at', [$created_from, $created_to])
                    ->get();
                $cash_outs = cash_out::where('account_id', $account->id)
                    ->whereBetween('created_at', [$created_from, $created_to])
                    ->get();
                $statements = $cash_ins->concat($cash_outs);
                break;
        }

        $writer = SimpleExcelWriter::streamDownload('account-statement.xlsx');

        $total_amount = 0;

        // sorting
        $statements = $statements->sortBy('created_at');

        while ($statement = $statements->shift()) {

            $total_amount = $total_amount + $statement->amount;

            $writer->addRow([
                'Account' => $account->owner->name,
                'Transaction Type' => $statement->transaction_type,
                'Date' => $statement->date,
                'Description' => $statement->description,
                'Transaction ID' => $statement->transaction_id,
                'name' => $statement->name,
                'username' => $statement->username,
                'amount' => $statement->amount,
                'old_balance' => $statement->old_balance,
                'new_balance' => $statement->new_balance,
                'note' => $statement->note,
            ]);
        }

        if ($transaction_type !== 'all') {
            $writer->addRow([
                'Account' => "",
                'Transaction Type' => "",
                'Date' => "",
                'Description' => "",
                'Transaction ID' => "",
                'name' => "",
                'username' => "Total",
                'amount' => $total_amount,
                'old_balance' => "",
                'new_balance' => "",
                'note' => "",
            ]);
        }

        $writer->toBrowser();
    }
}
