<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\nas;
use Illuminate\Http\Request;

class PPPoEProfileUploadCreateController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $routers = nas::where('mgid', $request->user()->id)->get();

        return view('admins.group_admin.ppp-profile-upload-create', [
            'routers' => $routers,
        ]);
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $request->validate([
            'router_id' => 'required|numeric',
        ]);

        return redirect()->route('routers.pppoe_profiles.create', ['router' => $request->router_id]);
    }
}
