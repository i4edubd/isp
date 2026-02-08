<?php

namespace App\Console\Commands;

use App\Models\operator;
use App\Models\pppoe_profile;
use Illuminate\Console\Command;

class PppoeProfileConstraintViolationCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'constraint_violation_check:pppoe_profile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check pppoe profiles constraint violation';

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

            $pppoe_profiles = pppoe_profile::where('mgid', $madmin->id)->get();

            foreach ($pppoe_profiles as $pppoe_profile) {

                if ($pppoe_profile->ipv4pool->mgid !== $pppoe_profile->mgid) {
                    $this->info('mgid: ' . $pppoe_profile->mgid . ' pppoe profile id: ' . $pppoe_profile->id);
                }
            }
        }

        return 0;
    }
}
