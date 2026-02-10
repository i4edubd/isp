<?php

namespace App\Http\Controllers;

use App\Models\vat_collection;
use Illuminate\Http\Request;

class VatCollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->filled('year')) {
            $request->validate([
                'year' => 'numeric',
            ]);
            $year = $request->year;
        } else {
            $year = date(config('app.year_format'));
        }

        $collections = vat_collection::where('mgid', $request->user()->id)
            ->where('year', $year)
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy('month');

        return view('admins.group_admin.vat-collections', [
            'collections' => $collections,
            'year' => $year,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\vat_collection  $vat_collection
     * @return \Illuminate\Http\Response
     */
    public function show(vat_collection $vat_collection)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\vat_collection  $vat_collection
     * @return \Illuminate\Http\Response
     */
    public function edit(vat_collection $vat_collection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\vat_collection  $vat_collection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, vat_collection $vat_collection)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\vat_collection  $vat_collection
     * @return \Illuminate\Http\Response
     */
    public function destroy(vat_collection $vat_collection)
    {
        //
    }
}
