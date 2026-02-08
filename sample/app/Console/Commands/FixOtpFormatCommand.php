<?php

namespace App\Console\Commands;

use App\Models\event_sms;
use Illuminate\Console\Command;

class FixOtpFormatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:OtpFormat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Temp';

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

        if (config('local.host_type') !== 'central') {
            return 0;
        }

        $event_smses = event_sms::where('event', 'OTP')->get();

        $this->info("Count: " . $event_smses->count());

        foreach ($event_smses as $event_sms) {
            $event_sms->default_sms = route('root') . '  এ ব্যবহার করার জন্য কোডঃ [OTP]';
            if (strlen($event_sms->operator_sms)) {
                $event_sms->operator_sms = route('root') . '  এ ব্যবহার করার জন্য কোডঃ [OTP]';
            }
            $event_sms->save();
        }

        return Command::SUCCESS;
    }
}
