<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DueDateReminderHelperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.due_date_reminders-helper');
                break;

            case 'operator':
                return view('admins.operator.due_date_reminders-helper');
                break;

            case 'sub_operator':
                return view('admins.sub_operator.due_date_reminders-helper');
                break;
        }
    }
}
