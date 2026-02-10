<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\nas;
use Illuminate\Http\Request;
use RouterOS\Sohag\RouterosAPI;

class WalledGardenController extends Controller
{

    /**
     * comment
     *
     * @var string
     */
    protected $comment = 'hotspot_walled_garden';

    /**
     * address-list
     *
     * @var string
     */
    protected $address_list = 'payment_gateways';

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Freeradius\nas  $router
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, nas $router)
    {
        $requester = $request->user();

        switch ($requester->role) {
            case 'group_admin':
                return view('admins.group_admin.router-walled-garden', [
                    'router' => $router,
                ]);
                break;

            case 'developer':
                return view('admins.developer.router-walled-garden', [
                    'router' => $router,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\nas  $router
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, nas $router)
    {
        $request->validate([
            'action' => 'required|in:updateorcreate,delete,delete_layer7',
        ]);

        // <<payment_gateways>>
        $PaymentGatewayController = new PaymentGatewayController();
        $payment_gateways = $PaymentGatewayController->getInternetPaymentGws($request->user());
        if (!$payment_gateways) {
            return redirect()->route('routers.index')->with('info', 'There is no online payment gateway to configure in walled-garden');
        }
        $payment_gateways = $payment_gateways->filter(function ($value, $key) {
            switch ($value->provider_name) {
                case 'bkash_checkout':
                    return true;
                    break;

                case 'bkash_tokenized_checkout':
                    return true;
                    break;

                case 'sslcommerz':
                    return true;
                    break;

                case 'nagad':
                    return true;
                    break;

                case 'shurjopay':
                    return true;
                    break;
            }
        });

        if ($payment_gateways->count() == 0) {
            return redirect()->route('routers.index')->with('info', 'There is no online payment gateway to configure in walled-garden');
        }

        // <<API>>
        $config  = [
            'host' => $router->nasname,
            'user' => $router->api_username,
            'pass' => $router->api_password,
            'port' => $router->api_port,
            'attempts' => 1
        ];
        $api = new RouterosAPI($config);
        if (!$api->connect($config['host'], $config['user'], $config['pass'])) {
            return redirect()->route('routers.index')->with('info', 'Could not connect to the router!');
        }

        // <<clear>>
        // ip firewall filter
        if ($request->action == 'delete' || $request->action == 'updateorcreate' || $request->action == 'delete_layer7') {
            $rows = $api->getMktRows('ip_firewall_filter', ['comment' => $this->comment]);
            $api->removeMktRows('ip_firewall_filter', $rows);
        }

        // walled-garden ip
        if ($request->action == 'delete' || $request->action == 'updateorcreate') {
            $rows = $api->getMktRows('walled_garden_ip', ['comment' => $this->comment]);
            $api->removeMktRows('walled_garden_ip', $rows);
        }

        // ip firewall layer7-protocol
        if ($request->action == 'delete' || $request->action == 'updateorcreate' || $request->action == 'delete_layer7') {
            $rows = $api->getMktRows('layer7_protocol', ['comment' => $this->comment]);
            $api->removeMktRows('layer7_protocol', $rows);
        }

        // ip firewall address-list
        if ($request->action == 'delete' || $request->action == 'updateorcreate') {
            $rows = $api->getMktRows('firewall_address_list', ['list' => $this->address_list]);
            $api->removeMktRows('firewall_address_list', $rows);
        }

        if ($request->action == 'delete' || $request->action == 'delete_layer7') {
            return redirect()->route('routers.index')->with('info', 'Rules removed successfully');
        }

        // <<updateorcreate>>
        $rows = [];
        foreach ($payment_gateways as $payment_gateway) {
            switch ($payment_gateway->provider_name) {
                case 'bkash_checkout':
                    $row = [];
                    $row['name'] = 'bkash_checkout';
                    $row['regexp'] = '^.+(bka.sh).*$';
                    $row['comment'] = $this->comment;
                    $rows['bkash_checkout'] = $row;
                    break;

                case 'bkash_tokenized_checkout':
                    $row = [];
                    $row['name'] = 'bkash_tokenized_checkout';
                    $row['regexp'] = '^.+(bkash.com).*$';
                    $row['comment'] = $this->comment;
                    $rows['bkash_tokenized'] = $row;
                    break;

                case 'sslcommerz':
                    $row = [];
                    $row['name'] = 'sslcommerz';
                    $row['regexp'] = '^.+(sslcommerz.com).*$';
                    $row['comment'] = $this->comment;
                    $rows['sslcommerz'] = $row;
                    $row = [];
                    $row['name'] = 'bkash_checkout';
                    $row['regexp'] = '^.+(bka.sh).*$';
                    $row['comment'] = $this->comment;
                    $rows['bkash_checkout'] = $row;
                    $row = [];
                    $row['name'] = 'bkash_tokenized_checkout';
                    $row['regexp'] = '^.+(bkash.com).*$';
                    $row['comment'] = $this->comment;
                    $rows['bkash_tokenized'] = $row;
                    $row = [];
                    $row['name'] = 'nagad';
                    $row['regexp'] = '^.+(mynagad.com).*$';
                    $row['comment'] = $this->comment;
                    $rows['nagad'] = $row;
                    break;

                case 'nagad':
                    $row = [];
                    $row['name'] = 'nagad';
                    $row['regexp'] = '^.+(mynagad.com).*$';
                    $row['comment'] = $this->comment;
                    $rows['nagad'] = $row;
                    break;

                case 'shurjopay':
                    $row = [];
                    $row['name'] = 'shurjopay';
                    $row['regexp'] = '^.+(shurjopayment.com).*$';
                    $row['comment'] = $this->comment;
                    $rows['shurjopay'] = $row;
                    $row = [];
                    $row['name'] = 'bkash_checkout';
                    $row['regexp'] = '^.+(bka.sh).*$';
                    $row['comment'] = $this->comment;
                    $rows['bkash_checkout'] = $row;
                    $row = [];
                    $row['name'] = 'bkash_tokenized_checkout';
                    $row['regexp'] = '^.+(bkash.com).*$';
                    $row['comment'] = $this->comment;
                    $rows['bkash_tokenized'] = $row;
                    $row = [];
                    $row['name'] = 'nagad';
                    $row['regexp'] = '^.+(mynagad.com).*$';
                    $row['comment'] = $this->comment;
                    $rows['nagad'] = $row;
                    break;
            }
        }

        // ip firewall layer7-protocol
        if (count($rows)) {
            $api->addMktRows('layer7_protocol', $rows);
        }

        // ip firewall filter
        $filter_rows = [];
        foreach ($rows as $row) {
            $filter_row = [];
            $filter_row['action'] = 'add-dst-to-address-list';
            $filter_row['address-list'] = $this->address_list;
            $filter_row['address-list-timeout'] = 'none-static';
            $filter_row['chain'] = 'forward';
            $filter_row['comment'] = $this->comment;
            $filter_row['layer7-protocol'] = $row['name'];
            $filter_rows[] = $filter_row;
        }
        $api->addMktRows('ip_firewall_filter', $filter_rows);

        // walled-garden ip
        $rows = [];
        $row = [];
        $row['action'] = 'accept';
        $row['dst-address-list'] = $this->address_list;
        $row['comment'] = $this->comment;
        $rows[] = $row;
        $api->addMktRows('walled_garden_ip', $rows);

        return redirect()->route('routers.index')->with('info', 'walled-garden configured successfully');
    }
}
