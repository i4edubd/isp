<?php

namespace App\Console\Commands;

use App\Http\Controllers\AllCustomerController;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Console\Command;

class UpdateAllCustomersFromCustomersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:all_customers_from_customers {mgid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update all customers from customers';

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

            AllCustomerController::updateOrCreate($customer);

            $bar->advance();
        }

        $bar->finish();

        return 0;
    }
}
