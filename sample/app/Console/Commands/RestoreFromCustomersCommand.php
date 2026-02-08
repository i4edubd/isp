<?php

namespace App\Console\Commands;

use App\Http\Controllers\Customer\HotspotCustomersRadAttributesController;
use App\Http\Controllers\Customer\PPPoECustomersRadAttributesController;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Console\Command;

class RestoreFromCustomersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:from_customers {mgid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore PostgreSQL pgsql_customers,pgsql_radchecks,pgsql_radreplies from MySQL customers table';

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

            if (operator::where('id', $customer->operator_id)->count()) {

                switch ($customer->connection_type) {
                    case 'PPPoE':
                        PPPoECustomersRadAttributesController::updateOrCreate($customer);
                        break;

                    case 'Hotspot':
                        HotspotCustomersRadAttributesController::updateOrCreate($customer);
                        break;
                }
            }

            $bar->advance();
        }

        $bar->finish();

        return 0;
    }
}
