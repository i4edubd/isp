<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\CacheController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\PppoeInterfaceTrafficMonitorController;
use App\Http\Controllers\RrdGraphApiController;
use App\Models\card_distributor;
use App\Models\customer_bill;
use App\Models\customer_payment;
use App\Models\Freeradius\radacct;
use App\Models\package;
use App\Models\pgsql\pgsql_radacct_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CustomersWebInterfaceController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function home(Request $request)
    {
        $all_customer = $request->user('customer');
        $operator = CacheController::getOperator($all_customer->operator_id);
        return view('customers.customer-home', [
            'operator' => $operator,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function profile(Request $request)
    {
        $all_customer = $request->user('customer');
        $customer = CacheController::getCustomerUseAllCustomer($all_customer);
        $operator = CacheController::getOperator($customer->operator_id);

        $cache_key = 'customer_radaccts_' . $customer->operator_id . '_' . $customer->id;
        $seconds = 300;
        $radaccts_history = Cache::remember($cache_key, $seconds, function () use ($customer, $operator) {
            $model = new pgsql_radacct_history();
            $model->setConnection($operator->pgsql_connection);
            return $model->where('username', $customer->username)->get();
        });

        return view('customers.customer-profile', [
            'customer' => $customer,
            'operator' => $operator,
            'radaccts_history' => $radaccts_history,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function radaccts(Request $request)
    {
        $all_customer = $request->user('customer');
        $customer = CacheController::getCustomerUseAllCustomer($all_customer);
        $operator = CacheController::getOperator($customer->operator_id);

        $cache_key = 'customer_radaccts_' . $customer->operator_id . '_' . $customer->id;
        $seconds = 300;
        $radaccts_history = Cache::remember($cache_key, $seconds, function () use ($customer, $operator) {
            $model = new pgsql_radacct_history();
            $model->setConnection($operator->pgsql_connection);
            return $model->where('username', $customer->username)->get();
        });

        return view('customers.customer-radaccts', [
            'customer' => $customer,
            'radaccts_history' => $radaccts_history,
            'operator' => $operator,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function graph(Request $request)
    {
        $all_customer = $request->user('customer');
        $customer = CacheController::getCustomerUseAllCustomer($all_customer);
        $operator = CacheController::getOperator($customer->operator_id);

        $cache_key = 'customer_graph_' . $customer->operator_id . '_' . $customer->id;
        $seconds = 300;
        $graph = Cache::remember($cache_key, $seconds, function () use ($customer) {
            return RrdGraphApiController::getImage($customer);
        });

        return view('customers.customer-graph', [
            'graph' => $graph,
            'operator' => $operator,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function packages(Request $request)
    {
        $request->validate([
            'sort' => 'nullable|in:price,validity',
        ]);

        $all_customer = $request->user('customer');
        $customer = CacheController::getCustomerUseAllCustomer($all_customer);

        // if a customer has a bill, customer package will change to package of id equal to package_id of the bill
        if ($customer->payment_status == 'billed') {
            return redirect()->route('customers.bills');
        }

        $operator = CacheController::getOperator($customer->operator_id);
        $connection_type = $customer->connection_type;

        $cache_key = "customer_packages_" . $connection_type . '_' . $operator->id;
        $ttl = 300;
        $packages = Cache::remember($cache_key, $ttl, function () use ($operator, $customer) {
            $package_where = [
                ['operator_id', '=', $operator->id],
                ['visibility', '=', 'public'],
            ];
            $packages = package::with('master_package')->where($package_where)->get();
            $packages = $packages->filter(function ($package)  use ($customer) {
                return $package->master_package->connection_type === $customer->connection_type;
            });
            return $packages;
        });

        if ($request->filled('sort')) {
            $packages = $packages->sortBy($request->sort);
        }

        // payment_gateways
        if ($operator->subscription_status !== 'active') {
            $payment_gateways = 0;
        } else {
            $PaymentGatewayController = new PaymentGatewayController();
            $payment_gateways = $PaymentGatewayController->getInternetPaymentGws($operator);
        }

        //return
        return view('customers.customers-packages', [
            'operator' => $operator,
            'packages' => $packages,
            'payment_gateways' => $payment_gateways,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function bills(Request $request)
    {
        $all_customer = $request->user('customer');
        $operator = CacheController::getOperator($all_customer->operator_id);

        // bills
        $bills_where = [
            ['operator_id', '=', $all_customer->operator_id],
            ['customer_id', '=', $all_customer->customer_id],
        ];
        $bills = customer_bill::where($bills_where)->get();

        // payment_gateways
        $PaymentGatewayController = new PaymentGatewayController();
        $payment_gateways = $PaymentGatewayController->getInternetPaymentGws($operator);

        //return
        return view('customers.customer-bills', [
            'operator' => $operator,
            'bills' => $bills,
            'payment_gateways' => $payment_gateways,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function cardStores(Request $request)
    {
        $all_customer = $request->user('customer');
        $operator = CacheController::getOperator($all_customer->operator_id);
        $card_distributors = card_distributor::where('operator_id', $operator->id)->get();

        return view('customers.customer-card-stores', [
            'operator' => $operator,
            'card_distributors' => $card_distributors,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function payments(Request $request)
    {
        $all_customer = $request->user('customer');
        $operator = CacheController::getOperator($all_customer->operator_id);

        // payments
        $payments_where = [
            ['operator_id', '=', $all_customer->operator_id],
            ['customer_id', '=', $all_customer->customer_id],
        ];
        $payments = customer_payment::where($payments_where)->get();

        return view('customers.customer-payments', [
            'operator' => $operator,
            'payments' => $payments,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $new_network
     * @return \Illuminate\Http\Response
     */
    public function networkCollision(Request $request, string $new_network)
    {
        $all_customer = $request->user('customer');
        $customer = CacheController::getCustomerUseAllCustomer($all_customer);
        $operator = CacheController::getOperator($customer->operator_id);
        return view('customers.found-in-other-network', [
            'operator' => $operator,
            'customer' => $customer,
            'new_network' => $new_network,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function liveTraffic(Request $request)
    {
        $all_customer = $request->user('customer');
        $customer = CacheController::getCustomerUseAllCustomer($all_customer);

        if ($customer->connection_type !== 'PPPoE') {
            return '0';
        }

        $operator = CacheController::getOperator($customer->operator_id);

        $cache_key = 'online_row_' . $customer->id;
        $ttl = 300;
        $radacct = Cache::remember($cache_key, $ttl, function () use ($customer, $operator) {
            $model = new radacct();
            $model->setConnection($operator->node_connection);
            $where = [
                ['operator_id', '=', $customer->operator_id],
                ['username', '=', $customer->username],
            ];
            return $model->where($where)->whereNull('acctstoptime')->first();
        });

        if (!$radacct) {
            return 0;
        }

        $controller = new PppoeInterfaceTrafficMonitorController();
        $data = $controller->show($radacct);

        $live_data = json_decode($data, true);

        if ($live_data['status'] !== "Online") {
            return 0;
        }

        return view('customers.customer-live-traffic', [
            'live_data' => collect($live_data),
        ]);
    }
}
