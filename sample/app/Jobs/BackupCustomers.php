<?php

namespace App\Jobs;

use App\Http\Controllers\Customer\CustomerBackupController;
use App\Models\backup_setting;
use App\Models\customer_backup_request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BackupCustomers implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The podcast instance.
     *
     * @var \App\Models\customer_backup_request
     */
    protected $customer_backup_request;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 3600;

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->customer_backup_request->backup_setting_id;
    }

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\customer_backup_request $customer_backup_request
     * @return void
     */
    public function __construct(customer_backup_request $customer_backup_request)
    {
        $this->customer_backup_request = $customer_backup_request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $backup_setting = backup_setting::find($this->customer_backup_request->backup_setting_id);
        CustomerBackupController::hotspot($backup_setting);
        CustomerBackupController::pppoe($backup_setting);
        $this->customer_backup_request->status = 'Done';
        $this->customer_backup_request->save();
    }
}
