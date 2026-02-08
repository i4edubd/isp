<?php

namespace App\Console\Commands;

use App\Http\Controllers\Customer\CustomerBillGenerateController;
use App\Models\all_customer;
use App\Models\billing_profile;
use App\Models\customer_zone;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\SimpleExcel\SimpleExcelReader;

class UpdateCustomerInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:CustomerInfo
                                {mgid : The ID of the Group Admin}
                                {file_name : The Excel File From which information will be updated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Customer Information';

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

        $file_name = $this->argument('file_name');

        $madmin = operator::find($mgid);

        $confirm_group = "Do you want to process for the master admin where email: " . $madmin->email . " name: " . $madmin->name;

        if (!$this->confirm($confirm_group)) {
            return 0;
        }

        $rows = SimpleExcelReader::create($file_name)->getRows();

        foreach ($rows as $rowProperties) {
            $username = trim($rowProperties['username']);
            $name = $rowProperties['name'];
            $mobile = validate_mobile($rowProperties['mobile']);
            $zone_id = $rowProperties['zone_id'];
            $package_expired_at = date_format($rowProperties['package_expired_at'], 'd-m-y');
            $operator_id = $rowProperties['operator_id'];

            $where = [
                ['mgid', '=', $mgid],
                ['username', '=', $username],
            ];

            $model = new customer();
            $model->setConnection($madmin->node_connection);
            $customer = $model->where($where)->first();

            if ($customer) {
                // name
                $customer->name = $name;
                $customer->save();
                // mobile
                if ($mobile) {

                    $duplicate = all_customer::where('mobile', $mobile)->count();

                    if ($duplicate == 0) {

                        $customer->mobile = $mobile;
                        $customer->save();

                        all_customer::updateOrCreate(
                            [
                                'mgid' => $customer->mgid,
                                'customer_id' => $customer->id,
                            ],
                            [
                                'operator_id' => $customer->operator_id,
                                'mobile' => $mobile,
                            ]
                        );
                    }
                }

                // zone_id
                $zone_where = [
                    ['operator_id', '=', $operator_id],
                    ['name', '=', $zone_id],
                ];

                $zone = customer_zone::where($zone_where)->firstOr(function () use ($operator_id, $zone_id) {
                    $zone = new customer_zone();
                    $zone->operator_id = $operator_id;
                    $zone->name = $zone_id;
                    $zone->save();
                    return $zone;
                });

                if ($zone) {
                    $customer->zone_id = $zone->id;
                    $customer->save();
                }

                // package_expired_at
                $now = Carbon::now();

                $exp_date = Carbon::createFromFormat('d-m-y', $package_expired_at);

                if ($exp_date->greaterThan($now)) {
                    $billing_profile = billing_profile::find($customer->billing_profile_id);
                    $next_payment_date = $billing_profile->next_payment_date;
                    $exp_date = Carbon::createFromFormat(config('app.date_format'), $next_payment_date)->isoFormat(config('app.expiry_time_format'));
                    $customer->package_expired_at = $exp_date;
                    $customer->save();
                } else {
                    CustomerBillGenerateController::generateBill($customer);
                }
            }
        }

        return 0;
    }
}
