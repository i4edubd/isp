<?php

namespace App\Http\Controllers;

use App\Models\operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SoftwareDemoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (config('consumer.demo_gid') == 0) {
            abort(404);
        }

        $operator = operator::findOrFail(config('consumer.demo_gid'));

        Auth::login($operator, true);

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }
}
