<?php

namespace App\Http\Controllers;

use App\Jobs\BackupCustomers;
use App\Models\backup_setting;
use App\Models\customer_backup_request;
use Illuminate\Http\Request;

class CustomerBackupRequestController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\backup_setting  $backup_setting
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, backup_setting $backup_setting)
    {
        $customer_backup_request = new customer_backup_request();
        $customer_backup_request->mgid = $request->user()->id;
        $customer_backup_request->backup_setting_id = $backup_setting->id;
        $customer_backup_request->save();

        $connection = config('app.env') == 'production' ? 'redis' : 'database';
        BackupCustomers::dispatch($customer_backup_request)
            ->onConnection($connection)
            ->onQueue('default');

        return redirect()->route('backup_settings.index')->with('success', 'Job created successfully!');
    }
}
