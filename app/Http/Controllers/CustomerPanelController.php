<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerPanelController extends Controller
{
    /**
     * Show the customer dashboard.
     */
    public function dashboard()
    {
        $customer = Auth::user()->customer; // Assuming a relationship is defined
        return view('customer.dashboard', compact('customer'));
    }

    /**
     * Show the customer's bills.
     */
    public function bills()
    {
        $bills = Auth::user()->customer->bills ?? []; // Assuming relationships
        return view('customer.bills', compact('bills'));
    }

    /**
     * Show the customer's payments.
     */
    public function payments()
    {
        $payments = Auth::user()->customer->payments ?? []; // Assuming relationships
        return view('customer.payments', compact('payments'));
    }
}
