<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\nas;
use Illuminate\Http\Request;

class ForeignRouterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'node' => 'required',
        ]);

        $model = new nas();
        $model->setConnection($request->node);

        $where = [
            ['mgid', '=', 0],
            ['location', '=', 'Unknown'],
        ];

        $routers = $model->where($where)->get();

        return view('admins.developer.foreign-routers', [
            'routers' => $routers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $nodes = explode(",", config('database.nodes'));

        return view('admins.developer.foreign-routers-create', [
            'nodes' => $nodes,
        ]);
    }
}
