<?php

namespace App\Http\Controllers;

use App\Models\disabled_menu;
use Illuminate\Http\Request;

class DisabledMenuController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $disabled_menus = disabled_menu::where('operator_id', $request->user()->id)->get();
        return match ($request->user()->role) {
            'group_admin' => view('admins.group_admin.disabled_menus_create', ['disabled_menus' => $disabled_menus]),
        };
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->mgid == config('consumer.demo_gid')) {
            return redirect()->route('admin.dashboard')->with('info', 'For Demo User Menu Settings Disabled');
        }

        disabled_menu::where('operator_id', $request->user()->id)->delete();
        CacheController::forgetDisabledMenus($request->user());

        $menus = match ($request->user()->role) {
            'group_admin' => config('sidebars.group_admin'),
        };

        foreach ($menus as $menu) {
            if ($request->filled($menu)) {
                continue;
            } else {
                $disabled_menu = new disabled_menu();
                $disabled_menu->operator_id = $request->user()->id;
                $disabled_menu->menu = $menu;
                $disabled_menu->save();
            }
        }

        return redirect()->route('admin.dashboard')->with('info', 'Menu Settings Saved');
    }
}
