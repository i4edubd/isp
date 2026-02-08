<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnalyzeRadiusSqlLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analyzeRadiusSqlLog {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze Radius Sql Log';

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

        $file = $this->argument('file');

        $lines = file($file);

        foreach ($lines as $line) {
            // $this->info($line);
            $username_between = Str::between($line, 'UserName', 'AND');
            $username = trim(Str::remove(['=', "'", ' '], $username_between));

            $update_string = Str::between($line, 'SET', 'WHERE');
            $this->info($update_string);
            $update_string = Str::remove(["'"], $update_string);
            $this->info($update_string);

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
                continue;
            }

            $updates['acctupdatetime'] = DB::raw($updates['acctupdatetime']);
            $updates['acctinputoctets'] = DB::raw($updates['acctinputoctets']);
            $updates['acctoutputoctets'] = DB::raw($updates['acctoutputoctets']);

            // $Interim_data = [
            //     'acctsessionid' => Str::replace("'", "", $updates['acctsessionid']),
            //     'acctuniqueid' => Str::replace("'", "", $updates['acctuniqueid']),
            //     'nasipaddress' => Str::replace("'", "", $updates['nasipaddress']),
            //     'callingstationid' => Str::replace("'", "", $updates['callingstationid']),
            //     'nasidentifier' => Str::replace("'", "", $updates['nasidentifier']),
            //     'acctupdatetime' => DB::raw($updates['acctupdatetime']),
            //     'framedipaddress' => Str::replace("'", "", $updates['framedipaddress']),
            //     'acctsessiontime' => $updates['acctsessiontime'],
            //     'acctinputoctets' => DB::raw(Str::replace("'", "", $updates['acctinputoctets'])),
            //     'acctoutputoctets' => DB::raw(Str::replace("'", "", $updates['acctoutputoctets'])),
            // ];

            dump($updates);

            return;

        }

        return Command::SUCCESS;
    }
}
