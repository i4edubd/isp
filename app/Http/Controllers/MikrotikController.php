<?php

namespace App\Http\Controllers;

use App\Models\Router;
use App\Services\RouterManagementService;
use App\Services\MikrotikServiceException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MikrotikController extends Controller
{
    public function index()
    {
        $routers = Router::all();
        return view('mikrotik.index', compact('routers'));
    }

    public function show(Router $router)
    {
        try {
            $routerService = new RouterManagementService($router);
            $ipPools = $routerService->getIpPools();
            $pppProfiles = $routerService->getPppProfiles();

            return view('mikrotik.show', compact('router', 'ipPools', 'pppProfiles'));
        } catch (MikrotikServiceException $e) {
            Log::error('Mikrotik service error', ['error' => $e->getMessage()]);
            return redirect()->route('mikrotik.index')->with('error', $e->getMessage());
        }
    }
}
