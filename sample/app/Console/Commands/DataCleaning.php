<?php

namespace App\Console\Commands;

use App\Http\Controllers\DataCleaningController;
use Illuminate\Console\Command;

class DataCleaning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Data Cleaning';

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
        DataCleaningController::monthly();
        DataCleaningController::yearly();
        DataCleaningController::biyearly();
        return 0;
    }
}
