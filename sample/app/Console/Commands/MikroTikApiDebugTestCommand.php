<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RouterOS\Sohag\RouterosAPI;

class MikroTikApiDebugTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:mikrotik_api {router_ip} {user} {password} {port}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'debug mikrotik api';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $config  = [
            'host' => $this->argument('router_ip'),
            'user' => $this->argument('user'),
            'pass' => $this->argument('password'),
            'port' => $this->argument('port'),
            'attempts' => 1,
            'debug' => true
        ];
        $api = new RouterosAPI($config);
        $api->connect($config['host'], $config['user'], $config['pass']);
        return Command::SUCCESS;
    }
}
