<?php

namespace App\Console\Commands;

use App\Models\Freeradius\radacct;
use App\Models\pgsql\pgsql_customer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RadSqlRelayV2P extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rad:sql_relay_v2p';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SQL Log to SQL Database V2 Production';

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
        $timestamp = Carbon::now()->timestamp;

        // db connection
        $db = config('database.connections.mysql.database');
        $db_user = config('database.connections.mysql.username');
        $db_password = config('database.connections.mysql.password');

        // Start Accounting-Request (Radaccts Insert)
        // The insert statement was made with DUAL in a way that can't generate a SQL error.
        if (file_exists('/var/log/freeradius/acct_start_raw.sql')) {

            $acct_start_file = '/var/log/freeradius/acct_start_' . $timestamp . '.sql';

            // copy and truncate accounting start log
            shell_exec("cp -f /var/log/freeradius/acct_start_raw.sql $acct_start_file");
            shell_exec('echo -n "" > /var/log/freeradius/acct_start_raw.sql');

            // start processing
            shell_exec("mysql --user=$db_user --password=$db_password --database=$db  < $acct_start_file");
            shell_exec("rm $acct_start_file");
        }


        if (file_exists('/var/log/freeradius/acct_interim_update_raw.sql')) {

            $interim_update_start_time = Carbon::now();
            $interim_update_file = '/var/log/freeradius/intrim_update_' . $timestamp . '.sql';

            // copy and truncate acct_interim_update log
            shell_exec("cp -f /var/log/freeradius/acct_interim_update_raw.sql $interim_update_file");
            shell_exec('echo -n "" > /var/log/freeradius/acct_interim_update_raw.sql');

            // interim_update variables
            $lines = [];
            $unique_lines = [];
            $Interim_Inserts = [];

            $lines = file($interim_update_file);
            while ($line = array_shift($lines)) {
                $username_between = Str::between($line, 'UserName', 'AND');
                $username = trim(Str::remove(['=', "'", ' '], $username_between));
                config(['cache.prefix' => '']);
                if (Cache::store('memcached')->has($username) == false) {
                    $unique_lines[$username] = $line;
                }
            }

            // Ends of Lines operation
            $lines = [];

            // update of updateOrInsert
            // The update statement was made in a way that can't generate a SQL error.
            $pdo = DB::connection()->getPdo();
            foreach ($unique_lines as $username => $line) {
                try {
                    $PDOStatement = $pdo->query($line);
                    $updated = $PDOStatement->rowCount();
                    if (!$updated) {
                        $Interim_Inserts[$username] = $line;
                    }
                } catch (\Throwable $th) {
                    Log::channel('sql_relay_error')->error('pdo query input : ' . $line . ' error: ' . $th);
                }
            }

            // End of Unique Lines operation
            $unique_lines = [];

            // Insert of updateOrInsert
            foreach ($Interim_Inserts as $username => $insert_line) {

                // if rowCount was false by PDOStatement
                $onlineCount = radacct::whereNull('acctstoptime')
                    ->where('username', $username)
                    ->count();

                if ($onlineCount) {
                    continue;
                }

                $update_string = Str::between($insert_line, 'SET', 'WHERE');
                $update_string = Str::remove(["'"], $update_string);
                $update_statement_array = explode(',', $update_string);

                $updates = [];
                foreach ($update_statement_array as $update_statement) {
                    $key_value_array =  explode("=", $update_statement);
                    if (count($key_value_array) == 2) {
                        $updates[trim($key_value_array['0'])] = trim($key_value_array['1']);
                    }
                }

                $keys_found = array_keys($updates);
                $expected_keys = [
                    'acctsessionid',
                    'acctuniqueid',
                    'nasipaddress',
                    'callingstationid',
                    'nasidentifier',
                    'acctupdatetime',
                    'framedipaddress',
                    'acctsessiontime',
                    'acctinputoctets',
                    'acctoutputoctets'
                ];
                $key_diff = array_diff($expected_keys, $keys_found);

                if (count($key_diff)) {
                    Log::channel('sql_relay_error')->error('Expected keys not found : ' . $insert_line);
                    continue;
                }

                $customer = pgsql_customer::where('username', $username)->first();

                if ($customer) {
                    try {
                        $radacct = new radacct();
                        $radacct->mgid = $customer->mgid;
                        $radacct->operator_id = $customer->operator_id;
                        $radacct->username = $customer->username;
                        $radacct->acctsessionid = $updates['acctsessionid'];
                        $radacct->acctuniqueid = $updates['acctuniqueid'];
                        $radacct->nasipaddress = $updates['nasipaddress'];
                        $radacct->callingstationid = $updates['callingstationid'];
                        $radacct->nasidentifier = $updates['nasidentifier'];
                        $radacct->acctterminatecause = "null";
                        $radacct->framedipaddress = $updates['framedipaddress'];
                        $radacct->acctstarttime = Carbon::now();
                        $radacct->acctupdatetime = Carbon::now();
                        $radacct->acctsessiontime = $updates['acctsessiontime'];
                        $radacct->acctinputoctets = DB::raw($updates['acctinputoctets']);
                        $radacct->acctoutputoctets = DB::raw($updates['acctoutputoctets']);
                        $radacct->save();
                    } catch (\Throwable $th) {
                        Log::channel('sql_relay_error')->error('new radacct create error: ' . $th);
                    }
                } else {
                    // Need to avoid hotspot clients like in radius setup?
                    config(['cache.prefix' => '']);
                    Cache::store('memcached')->add($username, $username, 600);
                }
            }

            // Ends of Interim_Inserts
            $Interim_Inserts = [];
            shell_exec("rm $interim_update_file");

            $interim_update_stop_time = Carbon::now();
            $processing_seconds = $interim_update_start_time->diffInSeconds($interim_update_stop_time);
            if ($processing_seconds > 120) {
                Log::channel('sql_relay_error')->warning('Processing Time greater than two minutes : ' . $processing_seconds . ' Seconds');
            }
        }

        // Stop Accounting-Request
        if (file_exists('/var/log/freeradius/acct_stop_raw.sql')) {

            $acct_stop_file = '/var/log/freeradius/acct_stop_' . $timestamp . '.sql';

            // copy and truncate accounting Stop log
            shell_exec("cp -f /var/log/freeradius/acct_stop_raw.sql $acct_stop_file");
            shell_exec('echo -n "" > /var/log/freeradius/acct_stop_raw.sql');

            // start processing
            // The update statement was made in a way that can't generate a SQL error.
            shell_exec("mysql --user=$db_user --password=$db_password --database=$db  < $acct_stop_file");
            shell_exec("rm $acct_stop_file");
        }

        return 0;
    }
}
