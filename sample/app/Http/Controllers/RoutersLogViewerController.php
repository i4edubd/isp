<?php

namespace App\Http\Controllers;

use App\Models\Freeradius\nas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use RouterOS\Sohag\RouterosAPI;

class RoutersLogViewerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Freeradius\nas  $nas
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, nas $router)
    {
        if ($request->user()->id !== $router->mgid) {
            abort(404);
        }

        $request->validate([
            'topics' => 'nullable|string',
            'message_like' => 'nullable|string',
        ]);

        $operator = $request->user();

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
            $router->api_status = 'Failed';
            $router->api_last_check = Carbon::now(getTimeZone($operator->id));
            $router->identity_status = 'incorrect';
            $router->save();
            return redirect()->route('routers.index')->with('error', 'API Failed');
        }

        $cache_key = "routers_logs_" . $request->user()->id;

        if ($request->filled('refresh')) {
            if (Cache::has($cache_key)) {
                Cache::forget($cache_key);
            }
        }

        $ttl = 300;
        $logs = Cache::remember($cache_key, $ttl, function () use ($api) {
            $com = '/log/print';
            $logs = $api->comm($com);
            krsort($logs);
            $logs = collect($logs);
            $logs = $logs->map(function ($item, $key) {
                if (is_array($item) || is_object($item)) {
                    return collect($item);
                }
                return $item;
            });
            return $logs;
        });

        // << topics
        $topics =  $logs->map(function ($item, $key) {
            return $item->get('topics');
        })->unique();
        // topics >>

        // << Filter
        if ($request->filled('topics')) {
            $filter_topics = $request->topics;
            $logs = $logs->filter(function ($item, $key) use ($filter_topics) {
                return $item->get('topics') == $filter_topics;
            });
        }

        if ($request->filled('message_like')) {
            $message_like = $request->message_like;
            $logs = $logs->filter(function ($item, $key) use ($message_like) {
                return false !== stristr($item->get('message'), $message_like);
            });
        }
        // Filter >>

        $length = 50;

        if ($request->filled('length')) {
            $length = $request->length;
        }

        $current_page = $request->input("page") ?? 1;

        $view_logs = new LengthAwarePaginator($logs->forPage($current_page, $length), $logs->count(), $length, $current_page, [
            'path' => $request->url(),
            'query' => $request->except('refresh'),
        ]);

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.routers-logs', [
                    'router' => $router,
                    'logs' => $view_logs,
                    'topics' => $topics,
                ]);
                break;

            case 'developer':
                return view('admins.developer.routers-logs', [
                    'router' => $router,
                    'logs' => $view_logs,
                    'topics' => $topics,
                ]);
                break;
        }
    }
}
