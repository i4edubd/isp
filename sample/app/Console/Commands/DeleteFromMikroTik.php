<?php

namespace App\Console\Commands;

use App\Models\Freeradius\customer;
use App\Models\Freeradius\nas;
use App\Models\operator;
use Illuminate\Console\Command;
use RouterOS\Sohag\RouterosAPI;

class DeleteFromMikroTik extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:from_mikrotik {gid} {nas_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $gid = $this->argument('gid');
        $nas_id = $this->argument('nas_id');

        $group_admin = operator::findOrFail($gid);

        $model = new nas();
        $model->setConnection($group_admin->node_connection);
        $router = $model->findOrFail($nas_id);

        if (!$router) {
            return 0;
        }

        $config  = [
            'host' => $router->nasname,
            'user' => $router->api_username,
            'pass' => $router->api_password,
            'port' => $router->api_port,
            'attempts' => 1
        ];

        $api = new RouterosAPI($config);

        if (!$api->connect($config['host'], $config['user'], $config['pass'])) {
            return 0;
        }


        $model = new customer();
        $model->setConnection($group_admin->node_connection);

        $customers = $model->where('gid', $gid)->get();

        foreach ($customers as $customer) {

            $get_rows = $api->getMktRows('ppp_secret', ['name' => $customer->username]);

            $api->removeMktRows('ppp_secret', $get_rows);
        }

        return 0;
    }
}
