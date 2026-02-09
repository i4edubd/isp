<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubResellerPanelController extends Controller
{
    public function index()
    {
        return view('sub-reseller.dashboard');
    }
}
