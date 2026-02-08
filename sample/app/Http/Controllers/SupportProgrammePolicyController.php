<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupportProgrammePolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('enrolInSupportProgramme');

        return view('admins.group_admin.support-programme-policy');
    }
}
