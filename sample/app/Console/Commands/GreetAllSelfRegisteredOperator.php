<?php

namespace App\Console\Commands;

use App\Http\Controllers\GreetSelfRegisteredOperatorsController;
use Illuminate\Console\Command;

class GreetAllSelfRegisteredOperator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketing:gasro';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Greeting email to all self registerd operator';

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
        GreetSelfRegisteredOperatorsController::greetAllOperator();
        return 0;
    }
}
