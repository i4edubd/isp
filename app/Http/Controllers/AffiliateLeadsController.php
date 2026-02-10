<?php

namespace App\Http\Controllers;

use App\Jobs\AssignDatabaseConnectionJob;
use App\Models\country;
use App\Models\language;
use App\Models\operator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AffiliateLeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('enrolInSupportProgramme');

        $leads = operator::where('marketer_id', $request->user()->id)->get();

        return view('admins.group_admin.affiliate-leads', [
            'leads' => $leads,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('enrolInSupportProgramme');

        // countries
        $countries = country::all();
        $country = $countries->firstWhere('id', '=', $request->user()->country_id);
        if ($country) {
            $countries =  $countries->prepend($country);
        }

        // languages
        $languages = language::all();
        $languages = $languages->prepend(getLanguage($request->user()));

        return view('admins.group_admin.leads-create', [
            'countries' => $countries,
            'languages' => $languages,
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
        $this->authorize('enrolInSupportProgramme');

        $request->validate([
            'country_id' => 'numeric|exists:countries,id|required',
            'lang_code' => 'string|exists:languages,code|required',
            'timezone' => 'string|exists:timezones,name|required',
            'company' => 'required|max:255',
            'name' => 'required',
            'mobile' => 'required',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:operators'],
            'password' => 'required',
        ]);

        $country = country::findOrFail($request->country_id);

        $mobile = validate_mobile($request->mobile, $country->iso2);

        $group_admin = new operator();
        $group_admin->marketer_id = $request->user()->id;
        $group_admin->sid = $request->user()->sid;
        $group_admin->country_id = $country->id;
        $group_admin->timezone = $request->timezone;
        $group_admin->lang_code = $request->lang_code;
        $group_admin->name = $request->name;
        $group_admin->email = $request->email;
        $group_admin->email_verified_at = Carbon::now(config('app.timezone'));
        $group_admin->password = Hash::make($request->password);
        $group_admin->company = $request->company;
        $group_admin->radius_db_connection = 'node1';
        $group_admin->mobile = $mobile;
        $group_admin->helpline = $mobile;
        $group_admin->role = 'group_admin';
        $group_admin->subscription_type = 'Paid';
        $group_admin->provisioning_status = 2;
        $group_admin->save();
        $group_admin->mgid = $group_admin->id;
        $group_admin->gid = $group_admin->id;
        $group_admin->save();

        // radius_db_connection
        AssignDatabaseConnectionJob::dispatch($group_admin)
            ->onConnection('database')
            ->onQueue('default');

        // suspended_users_pool
        SuspendedUsersPoolController::store($group_admin);

        return redirect()->route('affiliate-leads.index')->with('success', 'Lead has been created successfully');
    }
}
