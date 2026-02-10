<?php

namespace App\Http\Controllers;

use App\Models\nas_pppoe_profile;
use App\Models\Freeradius\nas;
use App\Models\operator;
use App\Models\pppoe_profile;
use RouterOS\Sohag\RouterosAPI;
use Illuminate\Http\Request;

class NasPppoeProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Freeradius\nas $router
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, nas $router)
    {
        if ($this->apiCheck($router) == 0) {
            return redirect()->route('routers.index')->with('error', 'Could not connect to the router.');
        }

        $operator = $request->user();

        $profiles = $this->uploadedProfiles($operator, $router);

        return view('admins.group_admin.routers-pppoe-profiles', [
            'profiles' => $profiles,
            'router' => $router,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Freeradius\nas $router
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, nas $router)
    {
        if ($this->apiCheck($router) == 0) {
            return redirect()->route('routers.index')->with('error', 'Could not connect to the router!');
        }

        $uploded_profiles = $this->uploadedProfiles($request->user(), $router);

        $all_profiles = pppoe_profile::where('mgid', $request->user()->id)->get();

        $profiles = $all_profiles->diff($uploded_profiles);

        return view('admins.group_admin.routers-pppoe-profiles-create', [
            'router' => $router,
            'uploded_profiles' => $uploded_profiles,
            'profiles' => $profiles,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\nas $router
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, nas $router)
    {

        //API Check
        $config  = [
            'host' => $router->nasname,
            'user' => $router->api_username,
            'pass' => $router->api_password,
            'port' => $router->api_port,
            'attempts' => 1,
            'debug' => false,
        ];

        $api = new RouterosAPI($config);

        if (!$api->connect($config['host'], $config['user'], $config['pass'])) {

            return redirect()->route('routers.index')->with('error', 'Could not connect to the router. Wrong API username or password.');
        }

        if ($request->filled('pppoe_profiles')) {

            if (count($request->pppoe_profiles)) {

                //delete previous records
                nas_pppoe_profile::where('nas_id', $router->id)->delete();

                $pppoe_profiles = pppoe_profile::whereIn('id', $request->pppoe_profiles)->get();

                //save for automatic update
                foreach ($pppoe_profiles as $pppoe_profile) {
                    $nas_pppoe_profile = new nas_pppoe_profile();
                    $nas_pppoe_profile->mgid = $router->mgid;
                    $nas_pppoe_profile->nas_id = $router->id;
                    $nas_pppoe_profile->pppoe_profile_id = $pppoe_profile->id;
                    $nas_pppoe_profile->save();
                    PppProfilePushController::store($pppoe_profile, $router);
                }
            }
        }

        return redirect()->route('routers.index')->with('success', 'PPP profiles uploaded successfully!');
    }


    /**
     * Check Router API
     *
     * @param  \App\Models\Freeradius\nas $router
     * @return  int 0 || 1
     */
    public function apiCheck(nas $router)
    {
        //API Check
        $config  = [
            'host' => $router->nasname,
            'user' => $router->api_username,
            'pass' => $router->api_password,
            'port' => $router->api_port,
            'attempts' => 1
        ];

        $api = new RouterosAPI($config);

        if ($api->connect($config['host'], $config['user'], $config['pass'])) {

            return 1;
        } else {
            return 0;
        }
    }



    /**
     * Get the uploaded profiles into the router
     *
     * @param  \App\Models\operator $operator
     * @param  \App\Models\Freeradius\nas $router
     * @return  Illuminate\Support\Collection
     */
    public function uploadedProfiles(operator $operator, nas $router)
    {
        $config  = [
            'host' => $router->nasname,
            'user' => $router->api_username,
            'pass' => $router->api_password,
            'port' => $router->api_port,
            'attempts' => 1
        ];

        $api = new RouterosAPI($config);

        $profiles = [];

        $ppp_profiles = $api->getMktRows('ppp_profile');

        while ($ppp_profile = array_shift($ppp_profiles)) {
            if (array_key_exists('name', $ppp_profile)) {
                $where = [
                    ['mgid', '=', $operator->id],
                    ['name', '=', $ppp_profile['name']],
                ];

                if (pppoe_profile::where($where)->count()) {
                    $profiles[] = pppoe_profile::where($where)->first();
                }
            }
        }

        if (count($profiles)) {
            return collect($profiles);
        } else {
            return collect([]);
        }
    }
}
