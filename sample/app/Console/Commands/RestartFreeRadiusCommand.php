<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class RestartFreeRadiusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restart:freeradius';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'restart freeradius service';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // memcached
        $process = new Process(['systemctl', 'restart', 'memcached.service']);
        $process->run();
        if ($process->isSuccessful()) {
            // Happy
        } else {
            Log::channel('restart_freeradius')->debug('memcached service restart failed : ' . Carbon::now());
        }

        // freeradius
        $process = new Process(['systemctl', 'restart', 'freeradius.service']);
        $process->run();
        if ($process->isSuccessful()) {
            // Happy
        } else {
            Log::channel('restart_freeradius')->debug('freeradius service restart failed : ' . Carbon::now());
        }

        return 0;
    }
}
