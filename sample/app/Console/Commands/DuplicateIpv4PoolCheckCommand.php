<?php

namespace App\Console\Commands;

use App\Http\Controllers\Ipv4poolController;
use App\Models\ipv4pool;
use App\Models\operator;
use Illuminate\Console\Command;
use Net_IPv4;

class DuplicateIpv4PoolCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'constraint_violation_check:duplicate_ipv4_pool';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Duplicate Ipv4 Pool';

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

        $ipv4lib = new Net_IPv4();

        $madmins = operator::where('role', 'group_admin')->get();

        foreach ($madmins as $madmin) {

            $ipv4pools = ipv4pool::where('mgid', $madmin->id)->get();

            foreach ($ipv4pools as $ipv4pool) {

                $network = long2ip($ipv4pool->subnet);
                $broadcast = long2ip($ipv4pool->broadcast);
                $this->info('checking IPv4pool:  mgid - ' . $madmin->id . ' pool id - ' . $ipv4pool->id);
                $this->info($network . '-' . $broadcast);

                $pools = $ipv4pools->except([$ipv4pool->id]);

                foreach ($pools as $pool) {
                    $subnet = long2ip($pool->subnet) . '/' . $pool->mask;
                    if ($ipv4lib->ipInNetwork($network, $subnet) || $ipv4lib->ipInNetwork($broadcast, $subnet)) {
                        $this->error('Duplicate with : mgid- ' . $madmin->id . ' pool id - ' . $pool->id);
                        $this->error(long2ip($pool->subnet) . '-' . long2ip($pool->broadcast));
                    }
                }
            }
        }

        return 0;
    }
}
