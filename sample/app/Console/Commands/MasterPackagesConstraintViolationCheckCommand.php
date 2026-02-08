<?php

namespace App\Console\Commands;

use App\Models\master_package;
use App\Models\operator;
use Illuminate\Console\Command;

class MasterPackagesConstraintViolationCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'constraint_violation_check:master_packages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check master packages constraint violation';

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
        if (config('local.host_type') !== 'central') {
            return 0;
        }

        $madmins = operator::where('role', 'group_admin')->get();

        foreach ($madmins as $madmin) {

            $mpackages = master_package::where('mgid', $madmin->id)
                ->where('connection_type', 'PPPoE')
                ->get();

            $this->info('checking ownership');

            foreach ($mpackages as $mpackage) {

                if ($mpackage->mgid !== $mpackage->pppoe_profile->mgid) {
                    $this->info('ownership violation => ' . ' mgid: ' . $madmin->id . ' master package id: ' . $mpackage->id);
                }
            }

            $this->info('checking ipv4 pool');

            foreach ($mpackages as $mpackage) {
                if (!$mpackage->pppoe_profile->ipv4pool->id) {
                    $this->info('error : ' . "IPv4 Pool Not Found" . " mgid " . $madmin->id . " profile id: " . $mpackage->pppoe_profile->id);
                }
            }
        }

        return 0;
    }
}
