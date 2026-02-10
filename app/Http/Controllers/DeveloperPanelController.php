<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeveloperPanelController extends Controller
{
    public function index()
    {
        $status = 'All systems are operational.';
        return view('developer.dashboard', compact('status'));
    }
}
