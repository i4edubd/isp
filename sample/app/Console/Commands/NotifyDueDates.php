<?php

namespace App\Console\Commands;

use App\Http\Controllers\DueDateNotificationController;
use App\Models\due_date_reminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyDueDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:due_dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify Due Dates';

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
        $today = Carbon::now(config('app.timezone'))->format('j');

        $reminder_where = [
            ['notification_date', '=', $today],
            ['automatic', '=', 'yes'],
        ];

        $reminders = due_date_reminder::where($reminder_where)->get();

        while ($due_date_reminder = $reminders->shift()) {

            DueDateNotificationController::pushNotifications($due_date_reminder);
        }

        return 0;
    }
}
