<?php

namespace App\Console\Commands;

use App\Http\Controllers\Customer\CustomerPackageUpdateController;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\package;
use Illuminate\Console\Command;

class UpdateRadChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:rad_checks {mgid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Rad Checks';

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
        $mgid = $this->argument('mgid');

        $madmin = operator::find($mgid);

        $info = "Name: " . $madmin->name . " Email: " . $madmin->email . " Company: " . $madmin->company;

        $this->info($info);

        if ($this->confirm('Do you wish to continue?') == false) {
            return 0;
        }

        $model = new customer();
        $model->setConnection($madmin->node_connection);
        $customers = $model->where('mgid', $mgid)->get();

        $bar = $this->output->createProgressBar(count($customers));

        $bar->start();

        while ($customer = $customers->shift()) {

            $package = package::find($customer->package_id);

            if ($package) {
                CustomerPackageUpdateController::update($customer, $package);
            }

            $bar->advance();
        }

        $bar->finish();

        return 0;
    }
}
