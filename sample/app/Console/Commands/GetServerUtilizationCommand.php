<?php

namespace App\Console\Commands;

use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Console\Command;

class GetServerUtilizationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:utilization';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Server Utilization Report';

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
        $madmins = operator::where('role', 'group_admin')->get();

        $radius_db_groups = $madmins->groupBy('radius_db_connection');

        $report = [];

        foreach ($radius_db_groups as $radius_db => $group_admins) {

            $server_report = [];
            $host = 'database.connections.' . $radius_db . '.host';
            $server_report["Name"] = $radius_db;
            $server_report["IP Address"] = config($host);
            $server_report["ISP Count"] = $group_admins->count();

            $users = 0;

            foreach ($group_admins as $group_admin) {
                $model = new customer();
                $model->setConnection($radius_db);
                $user_count = $model->where('mgid', $group_admin->id)->count();
                $users = $users + $user_count;
            }

            $server_report["User Count"] = $users;

            $report[] = $server_report;
        }

        print_r($report);

        return 0;
    }
}
