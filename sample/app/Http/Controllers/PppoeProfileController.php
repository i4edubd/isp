<?php

namespace App\Http\Controllers;

use App\Models\ipv4pool;
use App\Models\ipv6pool;
use App\Models\pppoe_profile;
use Illuminate\Http\Request;

class PppoeProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $madmin = $request->user();

        $profiles = pppoe_profile::with(['ipv4pool', 'ipv6pool', 'master_packages'])->where('mgid', $request->user()->id)->get();

        if ($request->filled('unused')) {
            $profiles = $profiles->filter(function ($profile) use ($madmin) {
                return $madmin->can('delete', $profile);
            });
        }

        return view('admins.group_admin.pppoe-profiles', [
            'profiles' => $profiles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $ipv4pools = ipv4pool::where('mgid', $request->user()->id)->get();

        $ipv6pools = ipv6pool::where('mgid', $request->user()->id)->get();

        return view('admins.group_admin.pppoe-profiles-create', [
            'ipv4pools' => $ipv4pools,
            'ipv6pools' => $ipv6pools,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'ipv4pool_id' => 'required',
            'ip_allocation_mode' => 'in:static,dynamic|required',
        ]);

        $name = getVarName($request->name);

        $where = [
            ['mgid', '=', $request->user()->id],
            ['name', '=', $name],
        ];

        if (pppoe_profile::where($where)->count()) {
            return redirect()->route('pppoe_profiles.create')->with('error', 'Duplicate PPPoE Profile Name');
        }

        $pppoe_profile = new pppoe_profile();
        $pppoe_profile->mgid = $request->user()->id;
        $pppoe_profile->name = $name;
        $pppoe_profile->ipv4pool_id = $request->ipv4pool_id;
        if ($request->filled('ipv6pool_id')) {
            $pppoe_profile->ipv6pool_id = $request->ipv6pool_id;
        }
        $pppoe_profile->ip_allocation_mode = $request->ip_allocation_mode;
        $pppoe_profile->save();
        return redirect()->route('pppoe_profiles.index')->with('success', 'PPPoE Profile has been created successfully!');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\pppoe_profile  $pppoe_profile
     * @return \Illuminate\Http\Response
     */
    public function destroy(pppoe_profile $pppoe_profile)
    {
        $this->authorize('delete', $pppoe_profile);

        if ($pppoe_profile->master_packages->count()) {
            return view('admins.group_admin.pppoe-profiles-delete-exception', [
                'pppoe_profile' => $pppoe_profile,
                'packages' => $pppoe_profile->master_packages,
            ]);
        }

        $pppoe_profile->delete();

        return redirect(url()->previous())->with('success', 'PPPoE Profile has been deleted successfully!');
    }

    /**
     * Check duplicate Profile Name.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $name
     * @return \Illuminate\View\View
     */
    public function checkDuplicateName(Request $request, string $name)
    {
        $name = getVarName($name);

        $where = [
            ['mgid', '=', $request->user()->id],
            ['name', '=', $name],
        ];

        if (pppoe_profile::where($where)->count()) {
            $duplicate = 1;
        } else {
            $duplicate = 0;
        }

        return view('laraview.layouts.duplicate-check-response', [
            'duplicate' => $duplicate,
        ]);
    }
}
