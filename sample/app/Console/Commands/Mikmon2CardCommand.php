<?php

namespace App\Console\Commands;

use App\Models\card_distributor;
use App\Models\master_package;
use App\Models\operator;
use App\Models\package;
use App\Models\recharge_card;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RouterOS\Sohag\RouterosAPI;

class Mikmon2CardCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mikmon2card {router_ip} {user} {password} {port} {oid} {card_distributor_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'mikmon 2  recharge card';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $operator = operator::findOrFail($this->argument('oid'));

        $info = "Name: " . $operator->name . " Email: " . $operator->email . " Company: " . $operator->company;
        $this->info($info);
        if ($this->confirm('Is the operator correct ?') == false) {
            return 0;
        }

        $card_distributor = card_distributor::findOrFail($this->argument('card_distributor_id'));
        $info = "Name: " . $card_distributor->name . " Store Name: " . $card_distributor->store_name . " Store Address : " . $card_distributor->store_address;
        $this->info($info);
        if ($this->confirm('Is the card distributor correct ?') == false) {
            return 0;
        }

        $config  = [
            'host' => $this->argument('router_ip'),
            'user' => $this->argument('user'),
            'pass' => $this->argument('password'),
            'port' => $this->argument('port'),
            'attempts' => 1
        ];
        $api = new RouterosAPI($config);
        if (!$api->connect($config['host'], $config['user'], $config['pass'])) {
            $this->error('could not connect to the router');
            return 0;
        }

        $rows = $api->getMktRows('hotspot_user');
        $this->info("Total Row Count: " . count($rows));

        $card_generated = 0;

        foreach ($rows as $row) {

            if (array_key_exists('profile', $row) == false) {
                Log::channel('debug')->debug(json_encode($row));
                continue;
            }

            $package = package::where('operator_id', $operator->id)->where('name', $row['profile'])->firstOr(function () use ($operator, $row) {

                $master_package = master_package::where('connection_type', 'Hotspot')
                    ->where('mgid', $operator->mgid)
                    ->where('name', $row['profile'])
                    ->first();

                if (!$master_package) {
                    $master_package = new master_package();
                    $master_package->mgid = $operator->mgid;
                    $master_package->connection_type = 'Hotspot';
                    $master_package->name = $row['profile'];
                    $master_package->speed_controller = 'Radius_Server';
                    $master_package->save();
                    $master_package = master_package::find($master_package->id);
                }

                if ($master_package) {
                    if ($operator->role == 'operator' || $operator->role == 'group_admin') {
                        $package = new package();
                        $package->mgid = $operator->mgid;
                        $package->gid = $operator->gid;
                        $package->operator_id = $operator->id;
                        $package->mpid = $master_package->id;
                        $package->name = $master_package->name;
                        $package->price = 1;
                        $package->save();
                        $package->ppid = $package->id;
                        $package->save();
                        return $package;
                    }

                    if ($operator->role == 'sub_operator') {
                        $gadmin = operator::find($operator->gid);
                        $package = new package();
                        $package->mgid = $gadmin->mgid;
                        $package->gid = $gadmin->gid;
                        $package->operator_id = $gadmin->id;
                        $package->mpid = $master_package->id;
                        $package->name = $master_package->name;
                        $package->price = 1;
                        $package->save();
                        $package->ppid = $package->id;
                        $package->save();

                        $sub_package = new package();
                        $sub_package->mgid = $operator->mgid;
                        $sub_package->gid = $operator->gid;
                        $sub_package->operator_id = $operator->id;
                        $sub_package->mpid = $master_package->id;
                        $sub_package->ppid = $package->id;
                        $sub_package->name = $master_package->name;
                        $sub_package->price = 1;
                        $sub_package->save();
                        return $sub_package;
                    }
                }

                return null;
            });

            if (!$package) {
                $this->info("Package Not Found");
            }

            if (array_key_exists('name', $row)) {
                if (recharge_card::where('operator_id', $operator->id)->where('pin', $row['name'])->count() == 0) {
                    $recharge_card = new recharge_card();
                    $recharge_card->operator_id = $operator->id;
                    $recharge_card->card_distributor_id = $card_distributor->id;
                    $recharge_card->package_id = $package->id;
                    $recharge_card->pin = $row['name'];
                    $recharge_card->creation_date = date(config('app.date_format'));
                    $recharge_card->creation_month = date(config('app.month_format'));
                    $recharge_card->creation_year = date(config('app.year_format'));
                    $recharge_card->save();
                    $card_generated++;
                }
            }
        }

        $this->info($card_generated . ' Card Generated');

        return Command::SUCCESS;
    }
}
