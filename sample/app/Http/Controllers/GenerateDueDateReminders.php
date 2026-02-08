<?php

namespace App\Http\Controllers;

use App\Models\due_date_reminder;
use App\Models\event_sms;
use Illuminate\Http\Request;

class GenerateDueDateReminders extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $reminders = [];

        $operator = $request->user();

        $event_sms = event_sms::where('event', 'DUE_NOTICE')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'DUE_NOTICE')
                    ->where('operator_id', 0)
                    ->first();
            });
        $message = SmsGenerator::getSMS($request->user(), $event_sms);

        for ($i = 2; $i < 29; $i++) {

            $where = [
                ['operator_id', '=', $request->user()->id],
                ['expiration_date', '=', $i],
                ['notification_date', '=', $i - 1],
            ];

            if (due_date_reminder::where($where)->count() == 0) {

                $reminders[] = due_date_reminder::make([
                    'operator_id' => $request->user()->id,
                    'expiration_date' => $i,
                    'notification_date' => $i - 1,
                    'automatic' => 'yes',
                    'message' => $message,
                ]);
            }
        }

        if (count($reminders) == 0) {
            return redirect()->route('due_date_reminders.index')->with('success', 'Nothing to generate!');
        }

        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.generate-due-date-reminders-preview', [
                    'reminders' => $reminders,
                ]);
                break;

            case 'operator':
                return view('admins.operator.generate-due-date-reminders-preview', [
                    'reminders' => $reminders,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.generate-due-date-reminders-preview', [
                    'reminders' => $reminders,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $operator = $request->user();

        $event_sms = event_sms::where('event', 'DUE_NOTICE')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'DUE_NOTICE')
                    ->where('operator_id', 0)
                    ->first();
            });
        $message = SmsGenerator::getSMS($request->user(), $event_sms);

        for ($i = 2; $i < 29; $i++) {

            $where = [
                ['operator_id', '=', $request->user()->id],
                ['expiration_date', '=', $i],
                ['notification_date', '=', $i - 1],
            ];

            if (due_date_reminder::where($where)->count() == 0) {

                due_date_reminder::create([
                    'operator_id' => $request->user()->id,
                    'expiration_date' => $i,
                    'notification_date' => $i - 1,
                    'automatic' => 'yes',
                    'message' => $message,
                ]);
            }
        }

        return redirect()->route('due_date_reminders.index')->with('success', 'Reminders Saved Successfully!');
    }
}
