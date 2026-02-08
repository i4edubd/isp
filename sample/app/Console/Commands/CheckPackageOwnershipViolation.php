<?php

namespace App\Console\Commands;

use App\Http\Controllers\CacheController;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Console\Command;

class CheckPackageOwnershipViolation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:package_ownership_violation {mgid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check package ownership violation';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $mgid = $this->argument('mgid');

        $madmin = operator::findOrFail($mgid);

        $info = "Name: " . $madmin->name . " Email: " . $madmin->email . " Company: " . $madmin->company;
        $this->info($info);

        if ($this->confirm('Do you wish to continue?') == false) {
            return 0;
        }

        $model = new customer();
        $model->setConnection($madmin->node_connection);
        $customers = $model->where('mgid', $madmin->id)->get();
        $violations = [];
        $violation = [];

        $this->info("Customer Count: " . $customers->count());

        foreach ($customers as $customer) {
            $package = CacheController::getPackage($customer->package_id);
            if ($package->operator_id != $customer->operator_id) {
                $this->info('mismatch found');
                $violation['operator_id'] = $customer->operator_id;
                $violation['customer_id'] = $customer->id;
                $violation['package_id'] = $package->id;
                $violation['package_name'] = $package->name;
                $violation['package_owner'] = $package->operator_id . "::" . $package->operator->name;
                $violations[] = collect($violation);
            }
        }

        if (count($violations)) {
            $violations = collect($violations);
            $violations = $violations->groupBy('operator_id');
            $this->info($violations->toJson());
        }

        return Command::SUCCESS;
    }
}
