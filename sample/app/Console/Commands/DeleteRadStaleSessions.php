<?php

namespace App\Console\Commands;

use App\Models\Freeradius\radacct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteRadStaleSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:rad_stale_sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Stale Sessions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Remove Duplicates
        $radaccts = radacct::whereNull('acctstoptime')->whereNotNull('update_time')->get();
        $unique_users = $radaccts->groupBy('username');

        if ($radaccts->count() != $unique_users->count()) {
            Log::channel('stale_sessions')->info('duplicate for the number of users: ' . $radaccts->count() - $unique_users->count());
            foreach ($unique_users as $unique_user) {
                if ($unique_user->count() > 1) {
                    $f_radacct = $unique_user->first();
                    $d_radaccts = $unique_user->except($f_radacct->id);
                    foreach ($d_radaccts as $d_radacct) {
                        $d_radacct->delete();
                    }
                }
            }
        }

        // Remove Stale Sessions
        $delete_count = radacct::whereNull('acctstoptime')->where('update_time', '<', now()->subMinutes(15)->format('Y-m-d H:i:s'))->delete();
        if ($delete_count > 0) {
            Log::channel('stale_sessions')->info('radaccts delete count: ' . $delete_count);
        }
        return 0;
    }
}
