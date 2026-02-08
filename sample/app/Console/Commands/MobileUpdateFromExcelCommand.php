<?php

namespace App\Console\Commands;

use App\Http\Controllers\AllCustomerController;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Spatie\SimpleExcel\SimpleExcelReader;

class MobileUpdateFromExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:mobiles {oid : Operator ID} {file_name : The Excel File} {--dry_run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Mobile Numbers from Excel';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dry_run = $this->option('dry_run');
        $oid = $this->argument('oid');
        $file_name = $this->argument('file_name');
        $operator = operator::findOrFail($oid);

        $info = 'Operator Email: ' . $operator->email . ' Operator Name: ' . $operator->nmae;

        $this->info($info);

        if (!$this->confirm('Do you want to continue?')) {
            return 0;
        }

        $rows = SimpleExcelReader::create($file_name)->getRows();

        $row_position = 2;

        foreach ($rows as $rowProperties) {

            $this->info('Processin row: ' . $row_position);

            // Mobile
            $mobile = validate_mobile($rowProperties['mobile']);
            if (!$mobile) {
                $mobile = null;
            }

            // Username
            $username = getUserName($rowProperties['username']);

            // customer
            if ($dry_run == false) {
                $model = new customer();
                $model->setConnection($operator->radius_db_connection);
                $customer = $model->where('operator_id', $operator->id)->where('username', $username)->first();
                if (!$customer) {
                    $this->info('Customer Not Found: ' . $username);
                }

                try {
                    $customer->mobile = $mobile;
                    $customer->save();
                    AllCustomerController::updateOrCreate($customer);
                } catch (\Throwable $th) {
                    Log::channel('debug')->debug($th->getTraceAsString());
                    continue;
                }
            } else {
                $this->info("Username: " . $username . " Mobile: " . $mobile);
            }

            $this->info('Mobile Updated Successfully. Row Position ' . $row_position);

            $row_position++;
        }

        return 0;
    }
}
