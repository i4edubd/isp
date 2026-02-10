<?php

namespace App\Http\Controllers;

use App\Models\complain_category;
use App\Models\customer_complain;
use Illuminate\Http\Request;

class ArchivedCustomerComplainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $requester = $request->user();

        if (!$requester) {
            abort(403);
        }

        if ($requester->role == 'manager') {
            $operator = $requester->group_admin;
        } else {
            $operator = $requester;
        }

        $filter = [];

        // default
        $filter[] = ['is_active', '=', 0];

        $filter[] = ['operator_id', '=', $operator->id];

        // category_id
        if ($request->filled('category_id')) {
            $filter[] = ['category_id', '=', $request->category_id];
        }

        if ($request->filled('month')) {
            $filter[] = ['month', '=', $request->month];
        }

        if ($request->filled('year')) {
            $filter[] = ['year', '=', $request->year];
        }

        // default length
        $length = 50;

        if ($request->filled('length')) {
            $length = $request->length;
        }

        $complain_categories = complain_category::where('operator_id', $operator->id)->get();

        $complaints = customer_complain::where($filter)->paginate($length);

        return view('complaint_management.archived_customer_complains', [
            'complain_categories' => $complain_categories,
            'complaints' => $complaints,
            'length' => $length,
        ]);
    }
}
