<?php

namespace App\Console\Commands;

use App\Http\Controllers\Customer\CustomerBackupController;
use Illuminate\Console\Command;

class BackupCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup Customers';

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
        CustomerBackupController::takeBackup();
        return 0;
    }
}
