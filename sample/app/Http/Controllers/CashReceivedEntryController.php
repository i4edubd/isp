<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Sms\SmsGatewayController;
use App\Models\account;
use App\Models\cash_out;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashReceivedEntryController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $role = $request->user()->role;

        $receivable_accounts = account::with(['provider'])->where('account_owner', $request->user()->id)->get();

        $account_owner = $request->user();

        $receivable_accounts = $receivable_accounts->filter(function ($value, $key) use ($account_owner) {
            return $account_owner->can('cashOut', $value);
        });

        switch ($role) {
            case 'super_admin':
                return view('admins.super_admin.entry-for-cash-received', [
                    'receivable_accounts' => $receivable_accounts,
                ]);
                break;

            case 'group_admin':
                return view('admins.group_admin.entry-for-cash-received', [
                    'receivable_accounts' => $receivable_accounts,
                ]);
                break;

            case 'operator':
                return view('admins.operator.entry-for-cash-received', [
                    'receivable_accounts' => $receivable_accounts,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.entry-for-cash-received', [
                    'receivable_accounts' => $receivable_accounts,
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
    public function store(Request $request)
    {
        $request->validate([
            'account_id' => 'required|numeric',
            'amount' => 'required|numeric|min:1'
        ]);

        $account = account::findOrFail($request->account_id);

        $this->authorize('cashOut', $account);

        if ($request->user()->id !== $account->account_owner) {
            abort(404);
        }

        //store cash out
        $cash_out = new cash_out();
        $cash_out->account_id = $account->id;
        $cash_out->transaction_code = 3;
        $cash_out->transaction_id = 0;
        $cash_out->name = $account->provider->name;
        $cash_out->username = $account->provider->email;
        $cash_out->amount = $request->amount;
        $cash_out->date = date(config('app.date_format'));
        $cash_out->old_balance = $account->balance;
        $cash_out->new_balance = $account->balance - $request->amount;
        $cash_out->month = date(config('app.month_format'));
        $cash_out->year = date(config('app.year_format'));
        $cash_out->note = $request->note;
        $cash_out->save();

        // update balance
        DB::transaction(function () use ($account, $cash_out) {
            $the_account = account::lockForUpdate()->find($account->id);
            $the_account->balance = $the_account->balance - $cash_out->amount;
            $the_account->save();
        });

        // SMS
        $message = SmsGenerator::paymentConfirmationMsg($request->user(), $cash_out->amount);
        $controller = new SmsGatewayController();
        $controller->sendSms($request->user(), $account->provider->mobile, $message);

        // return view
        return redirect()->route('accounts.receivable')->with('success', 'Transaction Saved successfully!');
    }
}
