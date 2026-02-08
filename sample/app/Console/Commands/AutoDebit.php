<?php

namespace App\Console\Commands;

use App\Http\Controllers\AutoDebitController;
use Illuminate\Console\Command;

class AutoDebit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:debit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adjust Advance Payment';

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
        AutoDebitController::store();
        return 0;
    }
}
