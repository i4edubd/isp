<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FeeListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        if ($operator->role !== 'group_admin') {
            abort(404);
        }

        $request->validate([
            'user_count' => 'nullable|numeric',
        ]);

        if ($request->filled('user_count')) {
            $calculate_result = getSubscriptionPrice($operator->id, $request->user_count);
        } else {
            $calculate_result = 0;
        }

        return view('admins.group_admin.fees', [
            'operator' => $operator,
            'calculate_result' => $calculate_result,
        ]);
    }
}
