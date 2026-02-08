<?php

namespace App\Http\Controllers;

use App\Models\pppoe_profile;

class PPPoeProfilePackagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\pppoe_profile  $pppoe_profile
     * @return \Illuminate\Http\Response
     */
    public function index(pppoe_profile $pppoe_profile)
    {
        return view('admins.group_admin.pppoe-profiles-packages', [
            'master_packages' => $pppoe_profile->master_packages,
        ]);
    }
}
