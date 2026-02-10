<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CardDistributorsPanelController extends Controller
{
    public function index()
    {
        return view('card-distributors.dashboard');
    }
}
