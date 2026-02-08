<?php

namespace App\Http\Controllers\Freeradius;

use App\Models\Freeradius\nas;
use RouterOS\Sohag\RouterosAPI;
use App\Http\Controllers\Controller;
use App\Models\backup_setting;
use App\Models\nas_pppoe_profile;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NasController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $where = [
            ['mgid', '=', $request->user()->mgid],
        ];

        $routers = nas::where($where)->get();

        $requester = $request->user();

        switch ($requester->role) {
            case 'group_admin':
                return view('admins.group_admin.routers', [
                    'routers' => $routers,
                ]);
                break;

            case 'developer':
                return view('admins.developer.routers', [
                    'routers' => $routers,
                ]);
                break;
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $requester = $request->user();

        switch ($requester->role) {
            case 'group_admin':
                return view('admins.group_admin.routers-create');
                break;

            case 'developer':
                return view('admins.developer.routers-create');
                break;
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validate
        $request->validate([
            'location' => ['required', 'string', 'max:255'],
            'nasname' => ['required', 'string'],
            'api_port' => ['required'],
            'api_username' => ['required'],
            'api_password' => ['required', 'min:8'],
            'overwrite_comment' => ['required'],
        ]);

        $debug = false;

        if ($request->filled('debug')) {
            if ($request->debug == 1) {
                $debug = true;
            }
        }

        //API Check
        $config  = [
            'host' => $request->nasname,
            'user' => $request->api_username,
            'pass' => $request->api_password,
            'port' => $request->api_port,
            'attempts' => 1,
            'debug' => $debug,
        ];

        $api = new RouterosAPI($config);

        if (!$api->connect($config['host'], $config['user'], $config['pass'])) {
            if ($debug == false) {
                return redirect()->route('routers.create')->with('error', 'Can not connect to the router! Check API port, username, password or port forwarding');
            } else {
                return;
            }
        }

        // check duplicate
        if (nas::where('nasname', $request->nasname)->exists()) {
            $unknown_nas = nas::where('nasname', $request->nasname)->first();
            if ($unknown_nas->mgid == 0 && $unknown_nas->location == 'Unknown') {
                $unknown_nas->mgid = $request->user()->mgid;
                $unknown_nas->location = $request->location;
                $unknown_nas->api_port = $request->api_port;
                $unknown_nas->api_username = $request->api_username;
                $unknown_nas->api_password = $request->api_password;
                $unknown_nas->overwrite_comment = $request->overwrite_comment;
                $unknown_nas->save();
                return redirect()->route('routers.index')->with('success', 'Router has been added successfully!');
            }
        }

        $nas = new nas();
        $nas->mgid = $request->user()->mgid;
        $nas->location = $request->location;
        $nas->nasname = $request->nasname;
        $nas->shortname = $request->nasname;
        $nas->secret = '5903963829';
        $nas->api_port = $request->api_port;
        $nas->api_username = $request->api_username;
        $nas->api_password = $request->api_password;
        $nas->overwrite_comment = $request->overwrite_comment;
        $nas->save();

        return redirect()->route('routers.configuration.create', ['router' => $nas->id])->with('success', 'Please Configure the router');

        return redirect()->route('routers.index')->with('success', 'Router has been added successfully!');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\nas  $router
     * @return \Illuminate\Http\Response
     */
    public function show(nas $router)
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

        if (!$api->connect($config['host'], $config['user'], $config['pass'])) {
            $router->api_status = 'Failed';
            $router->api_last_check = Carbon::now(getTimeZone($router->mgid));
            $router->identity_status = 'incorrect';
            $router->save();
            return redirect()->route('routers.index')->with('error', 'API Connect failed!');
        } else {
            $router->api_status = 'OK';
            $router->api_last_check = Carbon::now(getTimeZone($router->mgid));
            $router->identity_status = 'correct';
            $router->save();
            return redirect()->route('routers.index')->with('success', 'API Connect Successful!');
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Freeradius\nas $router
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, nas $router)
    {
        if ($request->user()->id !== $router->mgid) {
            abort(403);
        }

        $requester = $request->user();

        switch ($requester->role) {
            case 'group_admin':
                return view('admins.group_admin.routers-edit', [
                    'router' => $router,
                ]);
                break;

            case 'developer':
                return view('admins.developer.routers-edit', [
                    'router' => $router,
                ]);
                break;
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freeradius\nas  $router
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, nas $router)
    {
        if ($request->user()->id !== $router->mgid) {
            abort(403);
        }

        //validate
        $request->validate([
            'note' => ['nullable', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'nasname' => ['required', 'string'],
            'api_port' => ['required'],
            'api_username' => ['required'],
            'overwrite_comment' => ['required'],
            'api_password' => ['required', 'min:8'],
        ]);

        //API Check
        $config  = [
            'host' => $request->nasname,
            'user' => $request->api_username,
            'pass' => $request->api_password,
            'port' => $request->api_port,
            'attempts' => 1
        ];

        $api = new RouterosAPI($config);

        if (!$api->connect($config['host'], $config['user'], $config['pass'])) {

            return redirect()->route('routers.edit', ['router' => $router])->with('error', 'Can not connect to the router!');
        }

        $router->location = $request->location;
        $router->nasname = $request->nasname;
        $router->shortname = $request->nasname;
        $router->secret = '5903963829';
        $router->api_port = $request->api_port;
        $router->api_username = $request->api_username;
        $router->api_password = $request->api_password;
        $router->overwrite_comment = $request->overwrite_comment;
        $router->note = $request->note;
        $router->save();

        return redirect()->route('routers.index')->with('success', 'Router has been updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Freeradius\nas  $router
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,  nas $router)
    {
        if ($request->user()->id !== $router->mgid) {
            abort(403);
        }
        nas_pppoe_profile::where('mgid', $request->user()->id)->where('nas_id', $router->id)->delete();
        backup_setting::where('mgid', $request->user()->id)->where('nas_id', $router->id)->delete();
        $router->delete();
        return redirect()->route('routers.index')->with('success', 'Router has been deleted successfully!');
    }
}
