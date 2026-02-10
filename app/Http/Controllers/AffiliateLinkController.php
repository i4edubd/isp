<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AffiliateLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('enrolInSupportProgramme');

        $marketer_id = $request->user()->id;

        $ref = encryptOrDecrypt('encrypt', $marketer_id);

        $affiliate_link = route('register', ['ref' => $ref]);

        return view('admins.group_admin.affiliate-link', [
            'affiliate_link' => $affiliate_link,
        ]);
    }
}
