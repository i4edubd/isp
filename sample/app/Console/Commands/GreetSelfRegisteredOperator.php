<?php

namespace App\Console\Commands;

use App\Http\Controllers\GreetSelfRegisteredOperatorsController;
use Illuminate\Console\Command;

class GreetSelfRegisteredOperator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'marketing:gsro {operator_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Greeting email to self registerd operator';

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
        return GreetSelfRegisteredOperatorsController::greetOperator($this->argument('operator_id'));
        return 0;
    }
}
