<?php

namespace App\Http\Controllers;

use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Spatie\SimpleExcel\SimpleExcelWriter;

class CustomersInvoiceDownloadController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $operator = $request->user();

        $customer_zones = $operator->customer_zones;

        $packages = $operator->packages;

        $packages = $packages->filter(function ($package) {
            return $package->master_package->connection_type !== 'Hotspot';
        });

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.invoices-download-create', [
                    'customer_zones' => $customer_zones,
                    'packages' => $packages,
                ]);
                break;

            case 'operator':
                return view('admins.operator.invoices-download-create', [
                    'customer_zones' => $customer_zones,
                    'packages' => $packages,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.invoices-download-create', [
                    'customer_zones' => $customer_zones,
                    'packages' => $packages,
                ]);
                break;

            case 'manager':
                return view('admins.manager.invoices-download-create', [
                    'customer_zones' => $customer_zones,
                    'packages' => $packages,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'file_format' => 'required|in:PDF,excel',
            'sortby' => 'nullable|in:username,customer_id,package_id,customer_zone_id',
            'customer_zone_id' => 'nullable|numeric',
            'package_id' => 'nullable|numeric',
            'year' => 'nullable|numeric',
            'operator_id' => 'nullable|numeric',
        ]);

        if ($request->user()->role == 'manager') {
            $operator_id = $request->user()->group_admin->id;
        } else {
            $operator_id = $request->user()->id;
        }

        $where = [];

        if ($request->filled('operator_id')) {
            $where[] = ['operator_id', '=', $request->operator_id];
        } else {
            $where[] = ['operator_id', '=', $operator_id];
        }

        if ($request->filled('customer_zone_id')) {
            $where[] = ['customer_zone_id', '=', $request->customer_zone_id];
        }

        if ($request->filled('package_id')) {
            $where[] = ['package_id', '=', $request->package_id];
        }

        if ($request->filled('year')) {
            $where[] = ['year', '=', $request->year];
        }

        if ($request->filled('month')) {
            $where[] = ['month', '=', $request->month];
        }

        if ($request->filled('date')) {
            $where[] = ['due_date', '=', date_format(date_create($request->date), config('app.date_format'))];
        }

        $count_bill = customer_bill::where($where)->count();

        if ($count_bill == 0) {

            return redirect()->route('customers-invoice-download.create')->with('error', 'No Invoices found for the provided Inputs');
        }

        //operator

        $operator = operator::find($operator_id);

        //PDF
        if ($request->file_format == 'PDF') {

            if ($request->filled('sortby')) {
                $distinct_customers = customer_bill::where($where)
                    ->orderBy($request->sortby, 'asc')
                    ->get()
                    ->unique('customer_id');
            } else {
                $distinct_customers = customer_bill::where($where)
                    ->get()
                    ->unique('customer_id');
            }

            $counter = 1;

            $mpdf = new Mpdf();

            foreach ($distinct_customers as $distinct_customer) {

                $customer = customer::find($distinct_customer->customer_id);

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
                    ['operator_id', '=', $operator_id],
                    ['customer_id', '=', $customer->id],
                ];

                $bills = customer_bill::where($bills_where)->get();

                $total_amount = customer_bill::where($bills_where)->sum('amount');

                $invoice_table = view('admins.components.invoice-table', [
                    'bills' => $bills,
                    'total_amount' => $total_amount,
                ]);

                #total_x=200 and total_y=280

                if ($counter % 2) {
                    #<<envelope x=10-200 y=40-75
                    if (strlen($logo)) {
                        $mpdf->WriteFixedPosHTML($logo, 10, 40, 40, 35); //x= 10-50, y=40-75
                        $mpdf->WriteFixedPosHTML($operator_address, 60, 40, 65, 35); //x= 60-125, y=40-75
                    } else {
                        $mpdf->WriteFixedPosHTML($operator_address, 10, 40, 80, 35); //x= 10-90, y=40-75
                    }
                    $mpdf->WriteFixedPosHTML($customer_address, 135, 40, 65, 35, 'auto'); //x = 135-200, y=40-75
                    #envelope>>

                    #<<table x = 10-200 ,  y= 78-110(height=32)
                    $mpdf->WriteFixedPosHTML($invoice_table, 10, 78, 190, 32, 'auto');
                    #table>>

                    #<<Payment Received By: x = 10-190, y=111-121
                    $mpdf->WriteFixedPosHTML("Payment Received By:", 10, 111, 190, 10); //x= 10-200, y=111-121

                    $mpdf->WriteFixedPosHTML('<p style="text-decoration: overline;">Signature and Date</p>', 10, 122, 190, 5); //x= 10-200, y=122-127
                    #Payment Received By:>>

                } else {

                    #<<envelope x=10-200 y=175-210
                    if (strlen($logo)) {
                        $mpdf->WriteFixedPosHTML($logo, 10, 175, 40, 35); //x= 10-50, y=175-210
                        $mpdf->WriteFixedPosHTML($operator_address, 60, 175, 65, 35); //x= 60-125, y=175-210
                    } else {
                        $mpdf->WriteFixedPosHTML($operator_address, 10, 175, 80, 35); //x= 10-90, y=175-210
                    }
                    $mpdf->WriteFixedPosHTML($customer_address, 135, 175, 65, 35, 'auto'); //x = 135-200, y=175-210
                    #envelope>>

                    #<<table x = 10-200 , y= 213-245(height=32)
                    $mpdf->WriteFixedPosHTML($invoice_table, 10, 213, 190, 32, 'auto');
                    #table>>

                    #<<Payment Received By: x = 10-190, y=246-256
                    $mpdf->WriteFixedPosHTML("Payment Received By:", 10, 246, 190, 10); //x= 10-200, y=246-256

                    $mpdf->WriteFixedPosHTML('<p style="text-decoration: overline;">Signature and Date</p>', 10, 257, 190, 5); //x= 10-200, y=257-262
                    #Payment Received By:>>

                    $mpdf->AddPage();
                }

                $counter++;
            }

            $mpdf->Output("invoices.pdf", "D");
        }

        //excel
        if ($request->file_format == 'excel') {

            $bills = customer_bill::with('customer_zone')->where($where)->get();

            if ($request->filled('sortby')) {
                $bills = $bills->sortBy($request->sortby);
            }

            $total_amount = $bills->sum('amount');

            $total_operator_amount = $bills->sum('operator_amount');

            $writer = SimpleExcelWriter::streamDownload('invoices.xlsx');

            foreach ($bills as $bill) {

                $writer->addRow([
                    'Customer ID' => $bill->customer_id,
                    'Package Name' => $bill->description,
                    'Zone Name' => $bill->customer_zone->name,
                    'Customer Name' => $bill->name,
                    'mobile' => $bill->mobile,
                    'username' => $bill->username,
                    'amount' => $bill->amount,
                    'Upstream Amount' => $bill->operator_amount,
                    'billing_period' => $bill->billing_period,
                    'due_date' => $bill->due_date,
                ]);
            }

            $writer->addRow([
                'Customer ID' => "",
                'Package Name' => "",
                'Zone Name' => "",
                'Customer Name' => "",
                'mobile' => "",
                'username' => "Total",
                'amount' => $total_amount,
                'Upstream Amount' => $total_operator_amount,
                'billing_period' => "",
                'due_date' => "",
            ]);

            $writer->toBrowser();
        }
    }
}
