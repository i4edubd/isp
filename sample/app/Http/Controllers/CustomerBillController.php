<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customer\PPPoECustomersRadAttributesController;
use App\Http\Controllers\Customer\StaticIpCustomersFirewallController;
use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\package;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Mpdf\Mpdf;

class CustomerBillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', customer_bill::class);

        $request->validate([
            'customer_zone_id' => 'nullable|numeric',
            'package_id' => 'nullable|numeric',
            'operator_id' => 'nullable|numeric',
            'length' => 'nullable|numeric',
        ]);

        $requester = $request->user();

        $operator_id = $requester->id; // default

        switch ($requester->role) {
            case 'group_admin':
                $customer_bills = customer_bill::where('mgid', $requester->id)->where('processing', 0)->get();
                break;

            case 'operator':
                $customer_bills = customer_bill::where(function ($query) use ($requester) {
                    $query->where('gid', $requester->id)->orWhere('operator_id', $requester->id);
                })->where('processing', 0)
                    ->get();
                break;

            case 'sub_operator':
                $customer_bills = customer_bill::where('operator_id', $requester->id)->where('processing', 0)->get();
                break;

            case 'manager':
                $customer_bills = customer_bill::where('operator_id', $requester->gid)->where('processing', 0)->get();
                break;
        }

        if ($request->filled('customer_id')) {
            $customer_id = $request->customer_id;
            $customer_bills = $customer_bills->filter(function ($customer_bill) use ($customer_id) {
                return $customer_bill->customer_id == $customer_id;
            });
        } else {

            if ($requester->role == 'group_admin' || $requester->role == 'operator') {

                if ($request->filled('operator_id')) {
                    $operator_id = $request->operator_id;
                }

                $customer_bills = $customer_bills->filter(function ($customer_bill) use ($operator_id) {
                    return $customer_bill->operator_id == $operator_id;
                });
            }

            if ($request->filled('customer_zone_id')) {
                $customer_zone_id = $request->customer_zone_id;
                $customer_bills = $customer_bills->filter(function ($customer_bill) use ($customer_zone_id) {
                    return $customer_bill->customer_zone_id == $customer_zone_id;
                });
            }

            if ($request->filled('package_id')) {
                $package_id = $request->package_id;
                $customer_bills = $customer_bills->filter(function ($customer_bill) use ($package_id) {
                    return $customer_bill->package_id == $package_id;
                });
            }

            if ($request->filled('year')) {
                $year = $request->year;
                $customer_bills = $customer_bills->filter(function ($customer_bill) use ($year) {
                    return $customer_bill->year == $year;
                });
            }

            if ($request->filled('month')) {
                $month = $request->month;
                $customer_bills = $customer_bills->filter(function ($customer_bill) use ($month) {
                    return $customer_bill->month == $month;
                });
            }

            if ($request->filled('due_date')) {
                $due_date = date_format(date_create($request->due_date), config('app.date_format'));
                $customer_bills = $customer_bills->filter(function ($customer_bill) use ($due_date) {
                    return $customer_bill->due_date == $due_date;
                });
            }
        }

        $customers_amount = $customer_bills->sum('amount');

        $operators_amount = $customer_bills->sum('operator_amount');

        // default length
        $length = 50;

        if ($request->filled('length')) {
            $length = $request->length;
        }

        $current_page = $request->input("page") ?? 1;

        $bills = new LengthAwarePaginator($customer_bills->forPage($current_page, $length), $customer_bills->count(), $length, $current_page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        switch ($requester->role) {
            case 'group_admin':
                return view('admins.group_admin.customers-bills', [
                    'bills' => $bills,
                    'length' => $length,
                    'customers_amount' => $customers_amount,
                    'operators_amount' => ($operator_id == $requester->id) ? 0 : $operators_amount,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customers-bills', [
                    'bills' => $bills,
                    'length' => $length,
                    'customers_amount' => $customers_amount,
                    'operators_amount' => $operators_amount,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customers-bills', [
                    'bills' => $bills,
                    'length' => $length,
                    'customers_amount' => $customers_amount,
                    'operators_amount' => $operators_amount,
                ]);
                break;

            case 'manager':
                return view('admins.manager.customers-bills', [
                    'bills' => $bills,
                    'length' => $length,
                    'customers_amount' => $customers_amount,
                    'operators_amount' => $operators_amount,
                ]);
                break;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboardIndex(Request $request)
    {
        $bills = customer_bill::where('operator_id', $request->user()->id)
            ->where('due_date', date(config('app.date_format')))
            ->limit(3)
            ->get();
        $total_amount = customer_bill::where('operator_id', $request->user()->id)
            ->where('due_date', date(config('app.date_format')))->sum('amount');

        return view('admins.components.dashboard-customers-bills', [
            'bills' => $bills,
            'total_amount' => $total_amount,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $customer_bill
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $customer_bill)
    {
        $request->validate([
            'fieldname' => 'required|in:mobile,username'
        ]);

        $where = [];

        if ($request->user()->role == 'manager') {
            $where[] = ['operator_id', '=', $request->user()->gid];
        } else {
            $where[] = ['operator_id', '=', $request->user()->id];
        }

        $where[] = [$request->fieldname, '=', $customer_bill];

        $bills = customer_bill::where($where)->get();

        return view('admins.components.customers-bills-show', [
            'bills' => $bills,
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\customer_bill  $customer_bill
     * @return \Illuminate\Http\Response
     */
    public function printOrDownload(customer_bill $customer_bill)
    {
        $operator = operator::find($customer_bill->operator_id);

        $customer = customer::find($customer_bill->customer_id);

        #<<envelope
        if (strlen($operator->company_logo)) {
            $logo = "<img src=/storage/" . $operator->company_logo . ">";
        } else {
            $logo = "";
        }

        $operator_address = "<b>From, </b> <br>" . $operator->address;

        $customer_address = "<b>To, </b> <br>" . $customer->address . "<br> IP Address: " . $customer->login_ip;
        #envelope>>

        $bills_where = [
            ['operator_id', '=', $customer_bill->operator_id],
            ['customer_id', '=', $customer_bill->customer_id],
        ];

        $bills = customer_bill::where($bills_where)->get();

        $total_amount = customer_bill::where($bills_where)->sum('amount');

        $invoice_table = view('admins.components.invoice-table', [
            'bills' => $bills,
            'total_amount' => $total_amount,
        ]);


        #total_x=200 and total_y=280

        $mpdf = new Mpdf();

        #<<envelope x=10-200 y=40-75
        if (strlen($logo)) {
            $mpdf->WriteFixedPosHTML($logo, 10, 40, 40, 35); //x= 10-50, y=40-75
            $mpdf->WriteFixedPosHTML($operator_address, 60, 40, 65, 35); //x= 60-125, y=40-75
        } else {
            $mpdf->WriteFixedPosHTML($operator_address, 10, 40, 80, 35); //x= 10-90, y=40-75
        }
        $mpdf->WriteFixedPosHTML($customer_address, 135, 40, 65, 35, 'auto'); //x = 135-200, y=40-75
        #envelope>>

        #<<table x = 10-200 , y= 78-110(height=32)
        $mpdf->WriteFixedPosHTML($invoice_table, 10, 78, 190, 32, 'auto');
        #table>>

        #<<Payment Received By: x = 10-200, y=111-121
        $mpdf->WriteFixedPosHTML("Payment Received By:", 10, 111, 190, 10); //x= 10-200, y=111-121

        $mpdf->WriteFixedPosHTML('<p style="text-decoration: overline;">Signature and Date</p>', 10, 122, 190, 5); //x= 10-200, y=122-127

        $mpdf->Output();
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\customer_bill  $customer_bill
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, customer_bill $customer_bill)
    {
        $this->authorize('editInvoice', $customer_bill);

        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.customers-bill-edit', [
                    'customer_bill' => $customer_bill,
                ]);
                break;

            case 'operator':
                return view('admins.operator.customers-bill-edit', [
                    'customer_bill' => $customer_bill,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.customers-bill-edit', [
                    'customer_bill' => $customer_bill,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\customer_bill  $customer_bill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, customer_bill $customer_bill)
    {
        $this->authorize('editInvoice', $customer_bill);

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string',
            'billing_period' => 'required',
            'due_date' => 'required',
        ]);

        $customer_bill->amount = $request->amount;
        $customer_bill->description = $request->description;
        $customer_bill->billing_period = $request->billing_period;
        $customer_bill->due_date = date_format(date_create($request->due_date), config('app.date_format'));
        $customer_bill->save();

        // extending due date
        if ($customer_bill->wasChanged('due_date')) {
            $package_expired_at = Carbon::createFromFormat(config('app.date_format'), $customer_bill->due_date, getTimeZone($customer_bill->operator_id))->isoFormat(config('app.expiry_time_format'));
            $customer = customer::find($customer_bill->customer_id);
            $customer->package_expired_at = $package_expired_at;
            $customer->exptimestamp = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($customer_bill->operator_id), 'en')->timestamp;
            $customer->save();
        }

        return redirect()->route('customer_bills.index')->with('success', 'Customer Bill has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\customer_bill  $customer_bill
     * @return \Illuminate\Http\Response
     */
    public function destroy(customer_bill $customer_bill)
    {
        $this->authorize('deleteInvoice', $customer_bill);

        $operator_id = $customer_bill->operator_id;
        $customer_id = $customer_bill->customer_id;

        $customer_bill->delete();

        $where = [
            ['operator_id', '=', $operator_id],
            ['customer_id', '=', $customer_id],
        ];

        $count = customer_bill::where($where)->count();

        if ($count == 0) {

            $customer = customer::find($customer_id);
            if ($customer) {
                $customer->payment_status = 'paid';
                $customer->status = 'active';
                $customer->save();

                if ($customer->wasChanged('status')) {
                    if ($customer->connection_type == 'PPPoE') {
                        PPPoECustomersRadAttributesController::updateOrCreate($customer);
                        PPPCustomerDisconnectController::disconnect($customer);
                    }

                    if ($customer->connection_type == 'StaticIp') {
                        StaticIpCustomersFirewallController::updateOrCreate($customer);
                    }
                }
            }
        }

        return redirect()->route('customer_bills.index')->with('success', 'Bill has been deleted');
    }


    /**
     * Get Operator Amount.
     *
     * @param  \App\Models\customer_bill  $customer_bill
     * @return int
     */
    public static function operatorAmount(customer_bill $customer_bill)
    {
        $package = package::find($customer_bill->package_id);
        return round(($package->operator_price / $package->price) * $customer_bill->amount);
    }
}
