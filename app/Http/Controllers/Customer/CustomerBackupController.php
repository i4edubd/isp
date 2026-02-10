<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PppProfilePushController;
use App\Models\backup_setting;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\nas;
use App\Models\operator;
use App\Models\pppoe_profile;
use RouterOS\Sohag\RouterosAPI;

class CustomerBackupController extends Controller
{

    /**
     * Take Backup
     *
     * @return int
     */
    public static function takeBackup()
    {
        $backup_settings = backup_setting::where('backup_type', 'automatic')->get();

        foreach ($backup_settings as $backup_setting) {
            self::hotspot($backup_setting);
            self::pppoe($backup_setting);
        }

        return 0;
    }

    /**
     * Get Comment.
     *
     * @param \App\Models\Freeradius\customer $customer
     * @return string
     */
    public static function getComment(customer $customer)
    {
        return "oid--$customer->operator_id," .
            "zid--$customer->zone_id," .
            "name--$customer->name," .
            "mobile--$customer->mobile," .
            "bpid--$customer->billing_profile_id," .
            "exp_date--$customer->package_expired_at," .
            "ps--$customer->payment_status," .
            "status--$customer->status";
    }

    /**
     * Make a copy of hotspot customers into the specified router.
     *
     * @param \App\Models\backup_setting $backup_setting
     * @return int
     */
    public static function hotspot(backup_setting $backup_setting)
    {
        //operator
        $operator = operator::find($backup_setting->operator_id);

        if (!$operator) {
            $backup_setting->delete();
            return 0;
        }

        //Router
        $model = new nas();
        $model->setConnection($operator->radius_db_connection);
        $router = $model->find($backup_setting->nas_id);
        if (!$router) {
            return 0;
        }
        $config  = [
            'host' => $router->nasname,
            'user' => $router->api_username,
            'pass' => $router->api_password,
            'port' => $router->api_port,
            'timeout' => 10,
            'attempts' => 2
        ];
        $api = new RouterosAPI($config);

        if (!$api->connect($config['host'], $config['user'], $config['pass'])) {
            return 0;
        }

        //customers
        $customers_where = [
            ['connection_type', '=', 'Hotspot'],
            ['operator_id', '=', $operator->id],
        ];

        $model = new customer();

        $model->setConnection($operator->radius_db_connection);

        $customers = $model->where($customers_where)->get();

        if (count($customers)) {
            $rows = [];
            foreach ($customers as $customer) {
                $row = [];
                $row['name'] = $customer->username;
                $row['password'] = $customer->password;
                $row['comment'] = self::getComment($customer);
                $row['disabled'] = 'yes';
                $rows[] = $row;
            }
            $api->addMktRows('hotspot_user', $rows);
        }

        return 0;
    }


    /**
     * Make a copy of PPPoE customers into the specified router.
     *
     * @param \App\Models\backup_setting $backup_setting
     * @return int
     */
    public static function pppoe(backup_setting $backup_setting)
    {
        if ($backup_setting->primary_authenticator !== 'Radius') {
            return 0;
        }

        //operator
        $operator = CacheController::getOperator($backup_setting->operator_id);

        if (!$operator) {
            $backup_setting->delete();
            return 0;
        }

        //Router
        $model = new nas();
        $model->setConnection($operator->radius_db_connection);
        $router = $model->find($backup_setting->nas_id);

        if (!$router) {
            return 0;
        }

        $config  = [
            'host' => $router->nasname,
            'user' => $router->api_username,
            'pass' => $router->api_password,
            'port' => $router->api_port,
            'attempts' => 2
        ];

        $api = new RouterosAPI($config);

        if (!$api->connect($config['host'], $config['user'], $config['pass'])) {
            return 0;
        }

        //customers
        $customers_where = [
            ['connection_type', '=', 'PPPoE'],
            ['operator_id', '=', $operator->id],
        ];

        $model = new customer();
        $model->setConnection($operator->radius_db_connection);
        $customers = $model->where($customers_where)->get();

        if (count($customers)) {

            //baskets
            $profile_ids = [];
            $ppp_secrets = [];

            //collect data into the baskets
            foreach ($customers as $customer) {

                $package = CacheController::getPackage($customer->package_id);

                $master_package = $package->master_package;

                $pppoe_profile = $master_package->pppoe_profile;

                if ($package) {
                    $profile_ids[] = $pppoe_profile->id;
                    $ppp_secret = [];
                    $ppp_secret['name'] = $customer->username;
                    $ppp_secret['password'] = $customer->password;
                    $ppp_secret['profile'] = $pppoe_profile->name;
                    if ($pppoe_profile->ip_allocation_mode === 'static') {
                        $ppp_secret['remote-address'] = $customer->login_ip;
                    }
                    if ($router->overwrite_comment == 'yes') {
                        $ppp_secret['comment'] = self::getComment($customer);
                    }
                    $ppp_secret['disabled'] = 'yes';
                    $ppp_secrets[] = $ppp_secret;
                }
            }

            //profile backup start
            if (count($profile_ids)) {
                $profile_ids = array_unique($profile_ids);
                while ($profile_id = array_shift($profile_ids)) {
                    $pppoe_profile = pppoe_profile::find($profile_id);
                    if ($pppoe_profile->ip_allocation_mode === 'static') {
                        PppProfilePushController::store($pppoe_profile, $router);
                    }
                }
            }
            //profile backup stop

            //ppp secret backup start
            if (count($ppp_secrets)) {

                $new_secrets = [];

                while ($ppp_secret = array_shift($ppp_secrets)) {

                    $exist_rows = $api->getMktRows('ppp_secret', ["name" => $ppp_secret['name']]);

                    if (count($exist_rows)) {

                        $exist_row = array_shift($exist_rows);

                        $api->editMktRow('ppp_secret', $exist_row, $ppp_secret);
                    } else {

                        $new_secrets[] = $ppp_secret;
                    }
                }

                if (count($new_secrets)) {

                    $api->addMktRows('ppp_secret', $new_secrets);
                }
            }
            //ppp secret backup stop
        }
    }
}
