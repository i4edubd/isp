<?php

namespace App\Console\Commands;

use App\Http\Controllers\ActivateFupController;
use Illuminate\Console\Command;

class AutoActivateFup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activate:fup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Activate Fair Usage Policy';

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
        ActivateFupController::autoFup();
        return 0;
    }
}
