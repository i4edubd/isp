<?php

namespace App\Console\Commands;

use App\Mail\LowSmsBalance;
use App\Models\sms_gateway;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class CheckLowSmsBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:low_sms_balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Low SMS Balance';

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
        $where = [
            ['check_low_balance', '=', '1'],
            ['minimum_balance', '>', '0'],
        ];

        $sms_gws = sms_gateway::where($where)->get();

        foreach ($sms_gws as $sms_gw) {
            switch ($sms_gw->provider_name) {
                case 'bangladeshsms':
                    $report = Http::asForm()->get("http://bangladeshsms.com/miscapi/" . $sms_gw->password . "/getBalance");
                    $array = explode("BDT", $report);
                    $balance = trim($array[1]);
                    if ($balance < $sms_gw->minimum_balance) {
                        Mail::to($sms_gw->notification_subscriber)->send(new LowSmsBalance($balance));
                    }
                    break;

                case '880sms':
                    $report = Http::asForm()->get("https://880sms.com/miscapi/" . $sms_gw->password . "/getBalance");
                    $array = explode("BDT", $report);
                    $balance = trim($array[1]);
                    if ($balance < $sms_gw->minimum_balance) {
                        Mail::to($sms_gw->notification_subscriber)->send(new LowSmsBalance($balance));
                    }
                    break;
            }
        }

        return 0;
    }
}
