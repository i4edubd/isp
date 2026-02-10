<?php

namespace App\Http\Controllers;

use App\Models\vat_profile;
use Illuminate\Http\Request;

class VatProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $profiles = vat_profile::where('mgid', $request->user()->id)->get();

        return view('admins.group_admin.vat-profiles', [
            'profiles' => $profiles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', vat_profile::class);

        return view('admins.group_admin.vat-profiles-create');
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
            'description' => 'required|string',
            'identification_number' => 'required|string',
            'rate' => 'required|numeric',
            'status' => 'required|in:enabled,disabled',
        ]);

        $vat_profile = new vat_profile();
        $vat_profile->mgid = $request->user()->id;
        $vat_profile->description = $request->description;
        $vat_profile->identification_number = $request->identification_number;
        $vat_profile->rate = $request->rate;
        $vat_profile->status = $request->status;
        $vat_profile->save();

        return redirect()->route('vat_profiles.index')->with('success', 'Created Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\vat_profile  $vat_profile
     * @return \Illuminate\Http\Response
     */
    public function show(vat_profile $vat_profile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\vat_profile  $vat_profile
     * @return \Illuminate\Http\Response
     */
    public function edit(vat_profile $vat_profile)
    {
        $this->authorize('update', $vat_profile);

        return view('admins.group_admin.vat-profiles-edit', [
            'vat_profile' => $vat_profile,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\vat_profile  $vat_profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, vat_profile $vat_profile)
    {
        $this->authorize('update', $vat_profile);

        $request->validate([
            'description' => 'required|string',
            'identification_number' => 'required|string',
            'rate' => 'required|numeric',
            'status' => 'required|in:enabled,disabled',
        ]);

        $vat_profile->description = $request->description;
        $vat_profile->identification_number = $request->identification_number;
        $vat_profile->rate = $request->rate;
        $vat_profile->status = $request->status;
        $vat_profile->save();

        return redirect()->route('vat_profiles.index')->with('success', 'updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\vat_profile  $vat_profile
     * @return \Illuminate\Http\Response
     */
    public function destroy(vat_profile $vat_profile)
    {
        $this->authorize('delete', $vat_profile);

        $vat_profile->delete();

        return redirect()->route('vat_profiles.index')->with('success', 'deleted successfully');
    }
}
