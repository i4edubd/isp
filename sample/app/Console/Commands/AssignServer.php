<?php

namespace App\Console\Commands;

use App\Http\Controllers\GroupAdminController;
use Illuminate\Console\Command;

class AssignServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign New Server';

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

        echo GroupAdminController::assignDatabaseConnection();

        echo "\n";

        return 0;
    }
}
