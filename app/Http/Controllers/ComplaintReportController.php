<?php

namespace App\Http\Controllers;

use App\Models\complain_category;
use App\Models\customer_complain;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ComplaintReportController extends Controller
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

        if ($request->filled('year')) {
            $year = $request->year;
        } else {
            $year = date(config('app.year_format'));
        }

        $complain_categories = complain_category::where('operator_id', $operator->id)->get();

        $where = [];
        $where[0] = ['operator_id', '=', $operator->id];
        $where[1] = ['year', '=', $year];

        $monthly_report = [];

        for ($i = 1; $i <= 12; $i++) {
            $month = Carbon::create(date('01-' . $i . '-Y'))->format(config('app.month_format'));
            $where[2] = ['month', '=', $month];
            foreach ($complain_categories as $complain_category) {
                $where[3] = ['category_id', '=', $complain_category->id];

                $monthly_report[$month][] = [
                    "category_id" => $complain_category->id,
                    "category" => $complain_category->name,
                    "year" => $year,
                    "month" => $month,
                    "total_count" => customer_complain::where($where)->count(),
                ];
            }
        }

        $reports = [];

        foreach ($monthly_report as $month => $category_reports) {
            foreach ($category_reports as $category_report) {
                if ($category_report['total_count'] > 0) {
                    $reports[] = $category_report;
                }
            }
        }

        return view('complaint_management.complaint-reporting', [
            'reports' => $reports,
        ]);
    }
}
