<?php

namespace App\Console\Commands;

use App\Http\Controllers\AllCustomerController;
use App\Http\Controllers\Customer\PPPoECustomersRadAttributesController;
use App\Models\all_customer;
use App\Models\billing_profile;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\package;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportCustomersFromExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     * Example: php artisan ImportCustomers:FromExcel 1246 1346 MeghnaOnline.csv Y-m-d --dry_run
     *
     * @var string
     */
    protected $signature = 'ImportCustomers:FromExcel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Customers From Excel';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dry_run = (bool) $this->ask('dry_run? For dry run enter 1');
        $mgid = $this->ask('mgid?');
        $oid = $this->ask('oid?');
        $file_name = $this->ask('file_name?');
        $date_format = $this->ask('date_format?');
        $expiry_time_format = $this->ask('expiry_time_format ? Yes|No');

        $madmin = operator::findOrFail($mgid);
        $operator = operator::findOrFail($oid);

        $info = 'Admin: ' . $madmin->email . ' Operator: ' . $operator->email;

        $this->info($info);

        if (!$this->confirm('Do you want to continue?')) {
            return 0;
        }

        $rows = SimpleExcelReader::create($file_name)->getRows();

        $row_position = 2;

        foreach ($rows as $rowProperties) {

            $this->info('Processin row: ' . $row_position);

            // Mobile
            $mobile = validate_mobile($rowProperties['Mobile'], getCountryCode($oid));
            if (!$mobile) {
                $mobile = null;
            } else {
                if (all_customer::where('operator_id', $oid)->where('mobile', $mobile)->count()) {
                    Log::channel('debug')->debug('ImportCustomers:FromExcel >> duplicate mobile ' . $mobile . ' row position ' . $row_position);
                    $mobile = null;
                }
            }

            // BillingProfile
            $billing_profiles = billing_profile::where('mgid', $mgid)->get();
            $BillingProfile = $billing_profiles->where('name', $rowProperties['BillingProfile'])->first();
            if (!$BillingProfile) {
                $this->info('Billing Profile Not Found');
                return 0;
            }

            // Username
            $Username = getUserName($rowProperties['Username']);
            $model = new customer();
            $model->setConnection($madmin->radius_db_connection);
            if ($model->where('username', $Username)->count()) {
                //faild report
                Log::channel('debug')->debug('ImportCustomers:FromExcel >> duplicate Username ' . $Username . ' row position ' . $row_position);
                continue;
            }

            // Package
            $Package = package::where('mgid', $mgid)->where('operator_id', $oid)->where('name', trim($rowProperties['Package']))->firstOr(function () use ($rowProperties) {
                dump($rowProperties);
                abort(404);
            });

            // ExpDate
            // $ExpDate = date_format(date_create($rowProperties['ExpDate']), config('app.date_format'));
            if ($expiry_time_format == 'Yes') {
                $exp_date = Carbon::createFromIsoFormat(config('app.expiry_time_format'), trim($rowProperties['ExpDate']))->isoFormat(config('app.expiry_time_format'));
            } else {
                $ExpDate = Carbon::createFromFormat($date_format, trim($rowProperties['ExpDate']))->format(config('app.date_format'));
                $exp_date = Carbon::createFromFormat(config('app.date_format'), $ExpDate)->setHour(22)->isoFormat(config('app.expiry_time_format'));
            }

            if ($dry_run) {
                $this->info('exp in excel: ' . $rowProperties['ExpDate'] . ' formatted : ' . $exp_date);
            }

            try {
                $customer = new customer();
                $customer->setConnection($madmin->radius_db_connection);
                $customer->mgid = $operator->mgid;
                $customer->gid = $operator->gid;
                $customer->operator_id = $operator->id;
                $customer->connection_type = 'PPPoE';
                $customer->billing_type = $BillingProfile->billing_type;
                $customer->name = $rowProperties['Name'];
                $customer->mobile = $mobile;
                $customer->billing_profile_id = $BillingProfile->id;
                $customer->username = $Username;
                $customer->password = trim($rowProperties['Password']);
                $customer->package_id = $Package->id;
                $customer->package_name = $Package->name;
                $customer->package_started_at = Carbon::now()->isoFormat(config('app.expiry_time_format'));
                $customer->package_expired_at = $exp_date;
                $customer->exptimestamp = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')->timestamp;
                $customer->advance_payment = 0;
                $customer->payment_status = 'paid';
                $customer->status = 'active';
                $customer->nid = $rowProperties['NID'];
                $customer->house_no = $rowProperties['HouseNo'];
                $customer->thana = $rowProperties['Thana'];
                $customer->district = $rowProperties['District'];
                $customer->registration_date = date(config('app.date_format'));
                $customer->registration_week = date(config('app.week_format'));
                $customer->registration_month = date(config('app.month_format'));
                $customer->registration_year = date(config('app.year_format'));
                if ($dry_run == false) {
                    $customer->save();
                    $customer->parent_id = $customer->id;
                    $customer->save();
                }
            } catch (\Throwable $th) {
                Log::channel('debug')->debug($th->getTraceAsString());
                continue;
            }

            if ($dry_run == false) {
                AllCustomerController::updateOrCreate($customer);
                PPPoECustomersRadAttributesController::updateOrCreate($customer);
            }

            $this->info('Customer Imported Successfully. Row Position ' . $row_position);

            $row_position++;
        }

        return 0;
    }
}
