<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use App\Models\Freeradius\radacct;
use App\Models\pgsql\pgsql_radacct_history;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;

class InternetHistoryDownloadController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Freeradius\customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function create(customer $customer)
    {

        $count = pgsql_radacct_history::where('username', $customer->username)->count();

        if ($count == 0) {
            return redirect(url()->previous())->with('success', 'Nothing to Download');
        }

        $file = 'Internet-history-customer-' . $customer->id . '.xlsx';

        $radaccts = radacct::where('username', $customer->username)->get();

        $radaccts_histroy = pgsql_radacct_history::where('username', $customer->username)->get();

        $writer = SimpleExcelWriter::streamDownload($file);

        $total_download = 0;
        $total_upload = 0;

        foreach ($radaccts_histroy as $radacct_histroy) {

            $total_download = $total_download + $radacct_histroy->acctoutputoctets;
            $total_upload = $total_upload + $radacct_histroy->acctinputoctets;

            $writer->addRow([
                'Start Time' => $radacct_histroy->acctstarttime,
                'Stop Time' => $radacct_histroy->acctstoptime,
                'Total Time' => sToHms($radacct_histroy->acctsessiontime),
                'Terminate Cause' => $radacct_histroy->acctterminatecause,
                'Download(MB)' => $radacct_histroy->acctoutputoctets / 1000000,
                'Upload(MB)' => $radacct_histroy->acctinputoctets / 1000000,
            ]);
        }

        foreach ($radaccts as $radacct) {

            $total_download = $total_download + $radacct->acctoutputoctets;
            $total_upload = $total_upload + $radacct->acctinputoctets;

            $writer->addRow([
                'Start Time' => $radacct->acctstarttime,
                'Stop Time' => $radacct->acctstoptime,
                'Total Time' => sToHms($radacct->acctsessiontime),
                'Terminate Cause' => $radacct->acctterminatecause,
                'Download(MB)' => $radacct->acctoutputoctets / 1000000,
                'Upload(MB)' => $radacct->acctinputoctets / 1000000,
            ]);
        }

        $writer->addRow([
            'Start Time' => "",
            'Stop Time' => "",
            'Total Time' => "",
            'Terminate Cause' => "Total",
            'Download(MB)' => $total_download / 1000000,
            'Upload(MB)' => $total_upload / 1000000,
        ]);

        $writer->toBrowser();
    }
}
