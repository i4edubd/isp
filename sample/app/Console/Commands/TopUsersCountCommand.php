<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TopUsersCountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'count_top_users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'count top users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (config('local.host_type') !== 'central') {
            return Command::SUCCESS;
        }

        return Command::SUCCESS;
    }
}
