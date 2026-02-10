<?php

namespace App\Http\Controllers;

use App\Models\operator;
use App\Models\sales_comment;
use Illuminate\Http\Request;

class SalesCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function index(operator $operator)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, operator $operator)
    {
        $requester = $request->user();

        $comments = sales_comment::where('mgid', $operator->id)->get();

        switch ($requester->role) {
            case 'super_admin':
                return view('admins.super_admin.sales-comments', [
                    'operator' => $operator,
                    'comments' => $comments,
                ]);
                break;

            case 'developer':
                return view('admins.developer.sales-comments', [
                    'operator' => $operator,
                    'comments' => $comments,
                ]);
                break;

            case 'sales_manager':
                return view('admins.sales_manager.sales-comments', [
                    'operator' => $operator,
                    'comments' => $comments,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, operator $operator)
    {
        $request->validate([
            'new_comment' => 'required|string',
        ]);

        $sales_comment = new sales_comment();
        $sales_comment->mgid = $operator->id;
        $sales_comment->comment = $request->new_comment;
        $sales_comment->save();

        if ($operator->provisioning_status == 0) {
            $operator->provisioning_status = 1;
            $operator->save();
        }

        if ($request->filled('provisioned')) {
            $operator->provisioning_status = 2;
            $operator->save();
        }

        return redirect()->route('operators.sales_comments.create', ['operator' => $operator->id])->with('success', 'Comment saved successfully');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function nextOperator(Request $request, operator $operator)
    {

        $where = [
            ['role', '=', 'group_admin'],
            ['provisioning_status', '!=', 2],
        ];

        $group_admins = operator::where($where)
            ->orderBy('provisioning_status', 'asc')
            ->get();

        $found = 0;
        $first_operator_loaded = 0;
        $first_operator = $operator;
        $next_operator = $operator;

        foreach ($group_admins as $group_admin) {

            if ($first_operator_loaded == 0) {
                $first_operator = $group_admin;
                $first_operator_loaded = 1;
            }

            if ($found == 0) {
                if ($group_admin->id !== $operator->id) {
                    continue;
                } else {
                    $found = 1;
                }
            } else {
                $next_operator = $group_admin;
                break;
            }
        }

        if (!$next_operator) {
            $next_operator = $first_operator;
        }

        $requester = $request->user();

        $comments = sales_comment::where('mgid', $operator->id)->get();

        switch ($requester->role) {
            case 'super_admin':
                return view('admins.super_admin.sales-comments', [
                    'operator' => $next_operator,
                    'comments' => $comments,
                ]);
                break;

            case 'developer':
                return view('admins.developer.sales-comments', [
                    'operator' => $next_operator,
                    'comments' => $comments,
                ]);
                break;

            case 'sales_manager':
                return view('admins.sales_manager.sales-comments', [
                    'operator' => $next_operator,
                    'comments' => $comments,
                ]);
                break;
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\sales_comment  $sales_comment
     * @return \Illuminate\Http\Response
     */
    public function show(operator $operator, sales_comment $sales_comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\sales_comment  $sales_comment
     * @return \Illuminate\Http\Response
     */
    public function edit(operator $operator, sales_comment $sales_comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\sales_comment  $sales_comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, operator $operator, sales_comment $sales_comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\sales_comment  $sales_comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(operator $operator, sales_comment $sales_comment)
    {
        //
    }
}
