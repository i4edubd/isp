<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AllCustomerController;
use App\Http\Controllers\Customer\PPPoECustomersRadAttributesController;
use App\Models\all_customer;
use App\Models\billing_profile;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\package;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\SimpleExcel\SimpleExcelReader;

class PPPoEImportFromXLController extends Controller
{
    public function create(Request $request)
    {
        $operator = $request->user();
        $operators = $operator->operators->where('role', '!=', 'manager');

        return view('admins.group_admin.pppoe-customer-import', [
            'operators' => $operators,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'operator_id' => 'required|integer',
            'date_format' => 'required|string',
            'file' => 'required|file|mimes:xlsx',
        ]);

        $operator = operator::findOrFail($request->operator_id);
        $file = $request->file('file');
        $date_format = $request->date_format;

        $rows = SimpleExcelReader::create($file->getRealPath())->getRows();
        $results = [];

        foreach ($rows as $row) {
            $username = trim($row['username']);
            $mobile = validate_mobile($row['mobile']);
            $billing_profile = billing_profile::where('name', $row['billing_profile'])->first();
            $package = package::where('name', $row['package'])->first();
            $expiry_date = Carbon::createFromFormat($date_format, trim($row['expiry_date']))->format(config('app.date_format'));

            if (!$mobile || !$billing_profile || !$package) {
                $results[] = "Validation failed for username: $username";
                continue;
            }

            $customer = new customer();
            $customer->setConnection($operator->radius_db_connection);
            $customer->username = $username;
            $customer->mobile = $mobile;
            $customer->billing_profile_id = $billing_profile->id;
            $customer->package_id = $package->id;
            $customer->package_expired_at = $expiry_date;
            $customer->save();

            AllCustomerController::updateOrCreate($customer);
            PPPoECustomersRadAttributesController::updateOrCreate($customer);

            $results[] = "Successfully imported username: $username";
        }

        return view('admins.group_admin.pppoe-customer-import-result', [
            'results' => $results,
        ]);
    }
}