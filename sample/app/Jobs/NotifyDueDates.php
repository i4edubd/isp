<?php

namespace App\Jobs;

use App\Http\Controllers\DueDateNotificationController;
use App\Models\due_date_reminder;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyDueDates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The due_date_reminder instance.
     *
     * @var \App\Models\due_date_reminder
     */
    protected $due_date_reminder;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(due_date_reminder $due_date_reminder)
    {
        $this->due_date_reminder = $due_date_reminder;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $due_date_reminder = $this->due_date_reminder;

        DueDateNotificationController::pushNotifications($due_date_reminder);
    }
}
