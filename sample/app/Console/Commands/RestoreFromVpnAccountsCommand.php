<?php

namespace App\Console\Commands;

use App\Http\Controllers\VpnAccountController;
use App\Models\vpn_account;
use Illuminate\Console\Command;

class RestoreFromVpnAccountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:from_vpn_accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore PostgreSQL pgsql_customers,pgsql_radchecks,pgsql_radreplies from MySQL vpn_accounts table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $vpn_accounts = vpn_account::all();

        foreach ($vpn_accounts as $vpn_account) {
            VpnAccountController::updateOrCreateRadAttributes($vpn_account);
        }

        return 0;
    }
}
