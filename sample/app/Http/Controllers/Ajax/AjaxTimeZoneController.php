<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\country;
use App\Models\timezone;
use Illuminate\Http\Request;

class AjaxTimeZoneController extends Controller
{
    public function getTimeZones(Request $request)
    {
        $request->validate([
            'country_id' => 'required|numeric',
        ]);

        $country = country::findOrFail($request->country_id);

        $timezones = timezone::where('iso2', $country->iso2)->get();

        return view('ajax.timezone-options', [
            'timezones' => $timezones,
        ]);
    }
}
