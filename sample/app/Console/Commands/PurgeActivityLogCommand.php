<?php

namespace App\Console\Commands;

use App\Models\pgsql_activity_log;
use Illuminate\Console\Command;

class PurgeActivityLogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity-log:purge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge all activity logs older than the configurable amount of days.';

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
        if (config('local.host_type') == 'central') {
            pgsql_activity_log::where('created_at', '<', now()->subDays(config('consumer.activity_log_purge'))->format('Y-m-d H:i:s'))->delete();
        }

        return 0;
    }
}
