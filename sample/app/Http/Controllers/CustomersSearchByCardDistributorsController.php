<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\customer;
use Illuminate\Http\Request;

class CustomersSearchByCardDistributorsController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admins.card_distributors.search-customer');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
        ]);

        $card_distributor = $request->user('card');
        $operator = CacheController::getOperator($card_distributor->operator_id);

        $mobile = validate_mobile($request->mobile, getCountryCode($operator->id));
        if (!$mobile) {
            return redirect()->route('card.search-customer.create')->with('info', 'Invalid Mobile Number');
        }

        $model = new customer();
        $model->setConnection($operator->node_connection);
        $customer = $model->where('operator_id', $card_distributor->operator_id)->where('mobile', $mobile)->firstOrFail();

        if ($this->canPayBill($customer)) {
            return redirect()->route('card.customer.pay-bill.create', ['customer_id' => $customer->id]);
        }

        if ($this->canRecharge($customer)) {
            return redirect()->route('card.customer.recharge.create', ['customer_id' => $customer->id]);
        }

        return redirect()->route('card.search-customer.create')->with('info', 'The customer has been found but cannot be Top-up.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $customer_id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $customer_id)
    {
        $card_distributor = $request->user('card');
        $operator = CacheController::getOperator($card_distributor->operator_id);
        $model = new customer();
        $model->setConnection($operator->node_connection);
        $customer = $model->findOrFail($customer_id);
        return view('admins.card_distributors.customer-status', ['customer' => $customer]);
    }

    public function canRecharge(customer $customer)
    {
        return match ($customer->connection_type) {
            'PPPoE', 'Hotspot' => match ($customer->billing_type) {
                'Daily' => true,
                default => false
            },
            default => false
        };
    }

    public function canPayBill(customer $customer)
    {
        return match ($customer->connection_type) {
            'PPPoE', 'StaticIp', 'Other' => match ($customer->billing_type) {
                'Monthly' => match ($customer->payment_status) {
                    'billed' => true,
                    default => false
                },
                default => false
            },
            default => false
        };
    }
}
