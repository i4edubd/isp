<?php

namespace App\Http\Controllers;

use App\Models\due_date_reminder;
use App\Models\event_sms;
use Illuminate\Http\Request;

class DueDateReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $reminders = due_date_reminder::where('operator_id', $operator->id)->get();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.due_date_reminders', [
                    'reminders' => $reminders,
                ]);
                break;

            case 'operator':
                return view('admins.operator.due_date_reminders', [
                    'reminders' => $reminders,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.due_date_reminders', [
                    'reminders' => $reminders,
                ]);
                break;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
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

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.due_date_reminders-create', [
                    'message' => $message,
                    'event_sms' => $event_sms,
                    'message' => $message,
                ]);
                break;

            case 'operator':
                return view('admins.operator.due_date_reminders-create', [
                    'message' => $message,
                    'event_sms' => $event_sms,
                    'message' => $message,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.due_date_reminders-create', [
                    'message' => $message,
                    'event_sms' => $event_sms,
                    'message' => $message,
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
        $request->validate([
            'expiration_date' => 'required|string',
            'notification_date' => 'required|string',
            'automatic' => 'required|in:yes,no',
            'message' => 'required|string',
        ]);

        $expiration_date = date_format(date_create($request->expiration_date), 'j');

        $notification_date = date_format(date_create($request->notification_date), 'j');

        if ($notification_date > $expiration_date) {
            return redirect()->route('due_date_reminders.create')->with('info', 'The notification date should be less than the expiry date.');
        }

        $due_date_reminder = new due_date_reminder();
        $due_date_reminder->operator_id = $request->user()->id;
        $due_date_reminder->expiration_date = $expiration_date;
        $due_date_reminder->notification_date = $notification_date;
        $due_date_reminder->automatic = $request->automatic;
        $due_date_reminder->message = $request->message;
        $due_date_reminder->save();

        return redirect()->route('due_date_reminders.index')->with('success', 'Remainder saved successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\due_date_reminder  $due_date_reminder
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, due_date_reminder $due_date_reminder)
    {
        $operator = $request->user();

        $event_sms = event_sms::where('event', 'DUE_NOTICE')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'DUE_NOTICE')
                    ->where('operator_id', 0)
                    ->first();
            });

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.due_date_reminders-edit', [
                    'due_date_reminder' => $due_date_reminder,
                    'event_sms' => $event_sms,
                ]);
                break;

            case 'operator':
                return view('admins.operator.due_date_reminders-edit', [
                    'due_date_reminder' => $due_date_reminder,
                    'event_sms' => $event_sms,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.due_date_reminders-edit', [
                    'due_date_reminder' => $due_date_reminder,
                    'event_sms' => $event_sms,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\due_date_reminder  $due_date_reminder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, due_date_reminder $due_date_reminder)
    {
        $request->validate([
            'expiration_date' => 'required|string',
            'notification_date' => 'required|string',
            'automatic' => 'required|in:yes,no',
            'message' => 'required|string',
        ]);

        $expiration_date = date_format(date_create($request->expiration_date), 'j');

        $notification_date = date_format(date_create($request->notification_date), 'j');

        if ($notification_date > $expiration_date) {
            return redirect()->route('due_date_reminders.edit', ['due_date_reminder' => $due_date_reminder->id])->with('error', 'The notification date should be less than the expiry date.');
        }

        $due_date_reminder->expiration_date = $expiration_date;
        $due_date_reminder->notification_date = $notification_date;
        $due_date_reminder->automatic = $request->automatic;
        $due_date_reminder->message = $request->message;
        $due_date_reminder->save();

        return redirect()->route('due_date_reminders.index')->with('success', 'Remainder saved successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\due_date_reminder  $due_date_reminder
     * @return \Illuminate\Http\Response
     */
    public function destroy(due_date_reminder $due_date_reminder)
    {
        $due_date_reminder->delete();
        return redirect()->route('due_date_reminders.index')->with('success', 'Remainder Removed successfully!');
    }
}
