<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class laravelLogsClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel_logs:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Laravel Logs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $channels = config('logging.channels');

        foreach ($channels as $channel) {
            if (array_key_exists('path', $channel)) {
                $laravel_log = $channel['path'];
                if (file_exists($laravel_log)) {
                    shell_exec('echo -n "" > ' . $laravel_log);
                }
            }
        }

        $worker_log = storage_path('logs/worker.log');
        if (file_exists($worker_log)) {
            shell_exec('echo -n "" > ' . $worker_log);
        }

        return 0;
    }
}
