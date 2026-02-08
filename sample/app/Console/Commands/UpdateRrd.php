<?php

namespace App\Console\Commands;

use App\Http\Controllers\RrdGraphController;
use App\Models\Freeradius\radacct;
use Illuminate\Console\Command;
use RRDUpdater;

class UpdateRrd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:rrd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update RRD';

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

        $radaccts = radacct::with('customer')->whereNull('acctstoptime')->get();

        while ($radacct = $radaccts->shift()) {

            $customer = $radacct->customer;

            if (!$customer) {
                continue;
            }

            #RRD DB
            $rrd_db = RrdGraphController::getRrdDb($customer->id);

            if (file_exists($rrd_db) == false) {
                RrdGraphController::store($customer->id);
            }

            #Data Source
            $ds_upload = 'upload' . $customer->id;
            $ds_download = 'download' . $customer->id;
            $updator = new RRDUpdater($rrd_db);
            #View from router side
            $values = [
                $ds_download => $radacct->acctoutputoctets,
                $ds_upload => $radacct->acctinputoctets,
            ];
            $updator->update($values);
        }
        return 0;
    }
}
