<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeveloperPanelController extends Controller
{
    public function index()
    {
        return view('developer.dashboard');
    }
}
