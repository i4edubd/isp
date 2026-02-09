<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuperAdminPanelController extends Controller
{
    public function index()
    {
        return view('super-admin.dashboard');
    }
}
