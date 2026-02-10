<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customer\CustomerActivateController;
use App\Http\Controllers\Customer\PPPoECustomersExpirationController;
use App\Http\Controllers\Customer\PPPoECustomersFramedIPAddressController;
use App\Models\Freeradius\customer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SuspendDateEditController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, customer $customer)
    {
        $this->authorize('editSuspendDate', $customer);

        switch ($request->user()->role) {
            case 'group_admin':
                return view('admins.group_admin.edit-suspend-date', [
                    'customer' => $customer,
                ]);
                break;
            case 'manager':
                return view('admins.manager.edit-suspend-date', [
                    'customer' => $customer,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, customer $customer)
    {
        $request->validate([
            'package_expired_at' => 'required|string',
        ]);

        if ($request->package_expired_at === $customer->package_expired_at) {
            return redirect()->route('customers.index')->with('success', 'Nothing to update!');
        } else {
            $new_date = date_format(date_create($request->package_expired_at), config('app.date_format'));

            $package_expired_at = Carbon::createFromFormat(config('app.date_format'), $new_date, getTimeZone($customer->operator_id))->setHour(23)->setMinute(59)->isoFormat(config('app.expiry_time_format'));

            $customer->package_expired_at = $package_expired_at;
            $customer->exptimestamp = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer->operator_id), 'en')->timestamp;
            $customer->save();

            if ($customer->connection_type === 'PPPoE') {
                PPPoECustomersFramedIPAddressController::updateOrCreate($customer);
                PPPoECustomersExpirationController::updateOrCreate($customer);
            }

            if ($request->user()->can('activate', $customer)) {
                $controller = new CustomerActivateController();
                $controller->update($customer);
            }

            return redirect()->route('customers.index')->with('success', 'Suspend Date Updated Successfully');
        }
    }
}
