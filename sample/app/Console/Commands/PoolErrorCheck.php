<?php

namespace App\Console\Commands;

use App\Models\master_package;
use App\Models\package;
use Illuminate\Console\Command;

class PoolErrorCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:pool_error';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check pool Error';

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
        $packages = master_package::with('pppoe_profile.ipv4pool')->where('connection_type', 'PPPoE')->get();
        foreach ($packages as $package) {
            $profile_id = $package->pppoe_profile->id;
            $pool_id = $package->pppoe_profile->ipv4pool->id;
            if ($pool_id) {
            } else {
                echo "Error gid: $package->gid , package id: $package->id profile id: $profile_id pool id: $pool_id\n";
            }
        }
        echo "No Error Found \n";
        return 0;
    }
}
