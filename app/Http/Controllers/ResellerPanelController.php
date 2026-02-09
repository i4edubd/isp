<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResellerPanelController extends Controller
{
    public function index()
    {
        return view('reseller.dashboard');
    }
}
