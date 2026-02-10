<?php

namespace App\Http\Controllers;

use App\Models\account;
use App\Models\cash_in;
use App\Models\operator;
use Illuminate\Http\Request;

class SupportProgrammeSalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('enrolInSupportProgramme');

        $programme_director = operator::findOrFail(config('consumer.support_programme_director'));

        $marketer = $request->user();

        $account = account::where('account_provider', $programme_director->id)
            ->where('account_owner', $marketer->id)
            ->firstOr(function () use ($programme_director, $marketer) {
                $account = new account();
                $account->account_provider = $programme_director->id;
                $account->account_owner = $marketer->id;
                $account->balance = 0;
                $account->save();
                return $account;
            });

        $sales = cash_in::where('account_id', $account->id)->get();

        return view('admins.group_admin.support-programme-sales', [
            'account' => $account,
            'sales' => $sales,
        ]);
    }
}
