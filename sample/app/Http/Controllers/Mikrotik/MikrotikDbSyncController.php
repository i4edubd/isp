<?php

namespace App\Http\Controllers\Mikrotik;

use App\Http\Controllers\Controller;
use App\Models\customer_import_request;
use App\Models\Freeradius\nas;
use App\Models\Mikrotik\mikrotik_ip_pool;
use App\Models\Mikrotik\mikrotik_ppp_profile;
use App\Models\Mikrotik\mikrotik_ppp_secret;
use App\Models\operator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Net_IPv4;
use RouterOS\Sohag\RouterosAPI;

class MikrotikDbSyncController extends Controller
{

    /**
     * Import Mikrotik Resource into Mysql Database
     * for the Customer Import Request
     *
     * @param \App\Models\customer_import_request $customer_import_request
     *
     */
    public static function sync(customer_import_request $customer_import_request)
    {
        $group_admin = operator::find($customer_import_request->mgid);

        $model = new nas();

        $model->setConnection($group_admin->node_connection);

        $router = $model->find($customer_import_request->nas_id);

        //API
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

        $delete_where = [
            ['nas_id', '=', $router->id],
            ['mgid', '=', $group_admin->mgid],
        ];

        // Delete previously imported IP pool from the router.
        mikrotik_ip_pool::where($delete_where)->delete();

        // Import IP Pools froom router to MySQL
        $ip4pools = $api->getMktRows('ip_pool');

        while ($ip4pool =  array_shift($ip4pools)) {

            $ranges = self::parseIpPool($ip4pool['ranges']);

            if ($ranges == 0) {
                continue;
            }

            $ip_pool = new mikrotik_ip_pool();
            $ip_pool->customer_import_request_id = $customer_import_request->id;
            $ip_pool->mgid = $customer_import_request->mgid;
            $ip_pool->operator_id = $customer_import_request->operator_id;
            $ip_pool->nas_id = $customer_import_request->nas_id;
            $ip_pool->name = array_key_exists('name', $ip4pool) ? $ip4pool['name'] : "null";
            $ip_pool->ranges = $ranges;
            $ip_pool->save();
        }

        // Delete previously imported ppp profiles from the router.
        mikrotik_ppp_profile::where($delete_where)->delete();

        // Import ppp profiles from router to MySQL
        $ppp_profiles = $api->getMktRows('ppp_profile', ['default' => 'no']);

        while ($ppp_profile  = array_shift($ppp_profiles)) {
            $mikrotik_ppp_profile  = new mikrotik_ppp_profile();
            $mikrotik_ppp_profile->customer_import_request_id = $customer_import_request->id;
            $mikrotik_ppp_profile->mgid = $customer_import_request->mgid;
            $mikrotik_ppp_profile->operator_id = $customer_import_request->operator_id;
            $mikrotik_ppp_profile->nas_id = $customer_import_request->nas_id;
            $mikrotik_ppp_profile->name = array_key_exists('name', $ppp_profile) ? $ppp_profile['name'] : "Not Found";
            $mikrotik_ppp_profile->local_address = array_key_exists('local-address', $ppp_profile) ? $ppp_profile['local-address'] : "";
            $mikrotik_ppp_profile->remote_address = array_key_exists('remote-address', $ppp_profile) ? $ppp_profile['remote-address'] : "";
            $mikrotik_ppp_profile->save();
        }

        // Delete previously imported ppp secrets from router
        mikrotik_ppp_secret::where($delete_where)->delete();

        // take backup
        $now = Carbon::now()->timestamp;

        $file = 'ppp-secret-backup-by-billing'  . $now;

        $api->ttyWirte('/ppp/secret/export', ['file' => $file]);

        // Import ppp secrets from mikrotik to MySQl

        if ($customer_import_request->import_disabled_user == 'no') {
            $query = ['disabled' => 'no'];
        } else {
            $query = [];
        }

        $secrets = $api->getMktRows('ppp_secret', $query);

        while ($secret = array_shift($secrets)) {
            $mikrotik_ppp_secret = new mikrotik_ppp_secret();
            $mikrotik_ppp_secret->customer_import_request_id = $customer_import_request->id;
            $mikrotik_ppp_secret->mgid = $customer_import_request->mgid;
            $mikrotik_ppp_secret->operator_id = $customer_import_request->operator_id;
            $mikrotik_ppp_secret->nas_id = $customer_import_request->nas_id;
            $mikrotik_ppp_secret->name = $secret['name'];
            $mikrotik_ppp_secret->password = $secret['password'];
            $mikrotik_ppp_secret->profile = array_key_exists('profile', $secret) ? $secret['profile'] : "";
            $mikrotik_ppp_secret->comment = array_key_exists('comment', $secret) ? json_encode($secret['comment'], JSON_PARTIAL_OUTPUT_ON_ERROR) : "";
            $mikrotik_ppp_secret->disabled = array_key_exists('disabled', $secret) ? $secret['disabled'] : "";
            $mikrotik_ppp_secret->save();
        }

        return 1;
    }


    /**
     * Parse IP Pool Imported from Mikrotik Router
     *
     * @param string $ranges
     *
     */

    public static function parseIpPool(string $ranges)
    {

        $ipv4lib = new Net_IPv4();

        // <<comma separated pool
        $comma_separated = explode(',', $ranges);

        if (count($comma_separated) > 1) {

            $get_first_ip = 0;

            $network = '0.0.0.0';

            $broadcast = '0.0.0.0';

            foreach ($comma_separated as $range) {

                $range = trim($range);

                // slash_separated
                $slash_separated = explode('/', $range);

                if (count($slash_separated) == 2) {

                    $net = $ipv4lib->parseAddress($range);

                    if ($get_first_ip == 0) {
                        $network = $net->network;
                        $get_first_ip = 1;
                    }

                    $broadcast = $net->broadcast;
                }

                //  hyphen_separated
                $hyphen_separated = explode('-', $range);

                if (count($hyphen_separated) == 2) {

                    if ($get_first_ip == 0) {

                        $network = $hyphen_separated['0'];

                        $get_first_ip = 1;
                    }

                    $broadcast = $hyphen_separated['1'];
                }
            }

            $ranges = $network . '-' . $broadcast;
        }
        // comma separated pool>>

        #<<slash separated pool
        $range_array = explode('/', $ranges);

        $count = count($range_array);

        if ($count == 2) {
            $net = $ipv4lib->parseAddress($ranges);
            $network = $net->network;
            $mask = $net->bitmask;
            return $network . '/' . $mask;
        }

        // slash separated pool>>

        // <<Hyphen separated pool
        $range_array = explode('-', $ranges);

        $count = count($range_array);

        if ($count == 2) {

            $start = ip2long($range_array['0']);

            $last = ip2long($range_array['1']);

            $ip_count = abs($last - $start);

            if ($ip_count % 2 !== 1) {
                $ip_count = $ip_count + 3;
            }

            $total = ip2long("255.255.255.255");

            $mask = long2ip($total - $ip_count);

            $mask_array = explode(".", $mask);

            $mask_bit = 0;

            foreach ($mask_array as $partition) {

                if ($partition == 255) {

                    $mask_bit = $mask_bit + 8;
                } else {

                    //253-254
                    if ($partition > 252) {
                        $mask_bit = $mask_bit + 7;
                    } elseif

                    //249-252
                    ($partition > 248) {
                        $mask_bit = $mask_bit + 6;
                    } elseif

                    //241-248
                    ($partition > 240) {
                        $mask_bit = $mask_bit + 5;
                    } elseif

                    //225-240
                    ($partition > 224) {
                        $mask_bit = $mask_bit + 4;
                    } elseif

                    //193-224
                    ($partition > 192) {
                        $mask_bit = $mask_bit + 3;
                    } elseif

                    //129-192
                    ($partition > 128) {
                        $mask_bit = $mask_bit + 2;
                    } elseif

                    //1-128
                    ($partition > 0) {
                        $mask_bit = $mask_bit + 1;
                    }
                }
            }

            $ranges = $range_array['0'] . '/' . $mask_bit;

            $net = $ipv4lib->parseAddress($ranges);

            if (is_object($net)) {

                $network = $net->network;

                $mask = $net->bitmask;

                return $network . '/' . $mask;
            } else {

                return 0;
            }
        }

        return 0;
    }
}
