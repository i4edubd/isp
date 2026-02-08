<?php

namespace App\Console\Commands;

use App\Models\pgsql\pgsql_radreply;
use Illuminate\Console\Command;

class DeletePortLimitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:port_limit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete port limit';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count =  pgsql_radreply::where('attribute', 'Port-Limit')->delete();

        $this->info('Count: ' . $count);

        return 0;
    }
}
