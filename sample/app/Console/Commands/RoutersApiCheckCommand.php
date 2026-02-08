<?php

namespace App\Console\Commands;

use App\Http\Controllers\NasIdentifierController;
use App\Models\Freeradius\nas;
use Carbon\Carbon;
use Illuminate\Console\Command;
use RouterOS\Sohag\RouterosAPI;

class RoutersApiCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check_routers_api {debug=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Routers API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $debug = $this->argument('debug');

        $routers = nas::where('mgid', '>', 0)->get();

        foreach ($routers as $router) {
            if (!strlen($router->api_username)) {
                continue;
            }
            if (!strlen($router->api_password)) {
                continue;
            }

            $config  = [
                'host' => $router->nasname,
                'user' => $router->api_username,
                'pass' => $router->api_password,
                'port' => $router->api_port,
                'attempts' => 1,
                'debug' => $debug,
            ];

            $api = new RouterosAPI($config);

            if (!$api->connect($config['host'], $config['user'], $config['pass'])) {
                $router->api_status = 'Failed';
                $router->api_last_check = Carbon::now();
                $router->identity_status = 'incorrect';
                $router->save();
            } else {
                $router->api_status = 'OK';
                $router->api_last_check = Carbon::now();

                $com = '/system/identity/print';

                $system_identity = $api->comm($com);
                if ($debug) {
                    dump($system_identity);
                }

                $identity_array = array_shift($system_identity);
                if ($debug) {
                    dump($identity_array);
                }

                $identity = array_key_exists('name', $identity_array) ? $identity_array['name'] : "";
                if ($debug) {
                    dump($identity);
                }

                $valid_identity = NasIdentifierController::getNasIpAddress($identity);
                if ($debug) {
                    dump($valid_identity);
                }

                if ($valid_identity) {
                    $router->identity_status = 'correct';
                } else {
                    $router->identity_status = 'incorrect';
                }

                $router->save();
            }
        }

        return 0;
    }
}
