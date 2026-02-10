<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\package;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;

class BTRCReportController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $operators_where = [
            ['mgid', '=', $request->user()->id],
            ['role', '=', 'operator'],
        ];

        $operators = operator::where($operators_where)->get();
        $operators = $operators->push($request->user());

        return view('admins.group_admin.btrc-report-create', [
            'operators' => $operators,
        ]);
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
            'operator_id' => 'required|numeric',
        ]);

        $operator = operator::findOrFail($request->operator_id);

        $where = [
            ['gid', '=', $request->user()->id],
            ['operator_id', '=', $request->operator_id],
        ];

        $customers = customer::where($where)->get();

        $writer = SimpleExcelWriter::streamDownload('btrc_report.csv');

        foreach ($customers as $customer) {

            $package = package::findOrFail($customer->package_id);

            $writer->addRow([
                'name_operator' => $operator->company,
                'type_of_client' => $customer->type_of_client,
                'type_of_connection' => $customer->type_of_connection,
                'name_of_client' => $customer->name,
                'distribution Location point' => $customer->zone,
                'type_of_connectivity' => $customer->type_of_connectivity,
                'activation_date' => $customer->registration_date,
                'bandwidth_allocation MB' => $customer->package_name,
                'allowcated_ip' => $customer->login_ip,
                'house_no' => $customer->house_no,
                'road_no' => $customer->road_no,
                'area' => $customer->zone,
                'district' => $customer->district,
                'thana' => $customer->thana,
                'client_phone' => $customer->mobile,
                'mail' => $customer->email,
                'selling_bandwidth BDT excluding VAT' => $package->price,
            ]);
        }

        $writer->toBrowser();
    }
}
