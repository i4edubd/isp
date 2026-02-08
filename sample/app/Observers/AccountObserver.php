<?php

namespace App\Observers;

use App\Models\account;
use Illuminate\Support\Facades\Cache;

class AccountObserver
{
    /**
     * Handle the account "created" event.
     *
     * @param  \App\Models\account  $account
     * @return void
     */
    public function created(account $account)
    {
        //
    }

    /**
     * Handle the account "updated" event.
     *
     * @param  \App\Models\account  $account
     * @return void
     */
    public function updated(account $account)
    {
        if ($account->wasChanged('balance')) {
            $key = 'app_models_account_' . $account->account_provider;
            if (Cache::has($key)) {
                Cache::forget($key);
            }

            $key = 'app_models_account_' . $account->account_owner;
            if (Cache::has($key)) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Handle the account "deleted" event.
     *
     * @param  \App\Models\account  $account
     * @return void
     */
    public function deleted(account $account)
    {
        //
    }

    /**
     * Handle the account "restored" event.
     *
     * @param  \App\Models\account  $account
     * @return void
     */
    public function restored(account $account)
    {
        //
    }

    /**
     * Handle the account "force deleted" event.
     *
     * @param  \App\Models\account  $account
     * @return void
     */
    public function forceDeleted(account $account)
    {
        //
    }
}
