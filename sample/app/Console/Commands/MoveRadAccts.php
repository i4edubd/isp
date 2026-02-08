<?php

namespace App\Console\Commands;

use App\Models\Freeradius\customer;
use App\Models\Freeradius\radacct;
use App\Models\pgsql\pgsql_radacct_history;
use DateTime;
use Illuminate\Console\Command;

class MoveRadAccts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move:radaccts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move radaccts from MySQL to PgSQL';

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
        $radaccts = radacct::with('customer')
            ->whereNotNull('acctstoptime')
            ->limit(2400)
            ->get();

        $last_seen = [];

        while ($radacct = $radaccts->shift()) {
            if ($radacct->customer->id > 0) {

                // << mining last seen
                if (array_key_exists($radacct->username, $last_seen)) {
                    $last_timestamp = $last_seen[$radacct->username];
                } else {
                    $last_timestamp = 0;
                }

                $acctstoptime = new DateTime($radacct->acctstoptime);
                $acctstoptime_timestamp = $acctstoptime->getTimestamp();

                if ($acctstoptime_timestamp > $last_timestamp) {
                    $last_seen[$radacct->username] = $acctstoptime_timestamp;
                }
                // >>

                $histroy = new pgsql_radacct_history();
                $histroy->username = $radacct->username;
                $histroy->acctstarttime = $radacct->acctstarttime;
                $histroy->acctupdatetime = $radacct->acctupdatetime;
                $histroy->acctstoptime = $radacct->acctstoptime;
                $histroy->acctinterval = $radacct->acctinterval;
                $histroy->acctsessiontime = $radacct->acctsessiontime;
                $histroy->acctinputoctets = $radacct->acctinputoctets;
                $histroy->acctoutputoctets = $radacct->acctoutputoctets;
                $histroy->callingstationid = $radacct->callingstationid;
                $histroy->framedipaddress = $radacct->framedipaddress;
                $histroy->save();
            }
            $radacct->delete();
        }

        // save last_seen_timestamp
        foreach ($last_seen as $key => $value) {
            $customer = customer::where('username', $key)->first();
            $customer->last_seen_timestamp = $value;
            $customer->save();
        }

        return 0;
    }
}
