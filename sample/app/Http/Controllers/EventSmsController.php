<?php

namespace App\Http\Controllers;

use App\Models\event_sms;
use Illuminate\Http\Request;

class EventSmsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();

        $event_smses = event_sms::where('operator_id', 0)
            ->orWhere('operator_id', $operator->id)
            ->get();

        $default_count = $event_smses->where('operator_id', 0)->count();

        if ($event_smses->count() !== $default_count) {

            $event_smses = $event_smses->groupBy('event')->map(function ($values) {
                if ($values->count() > 1) {
                    return $values->where('operator_id', '>', 0)->first();
                } else {
                    return $values->first();
                }
            });
        }

        $lang_code = strlen($operator->lang_code) ? $operator->lang_code : config('consumer.lang_code');
        $default_attribute = 'default_sms_' . $lang_code;

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.sms-events', [
                    'event_smses' => $event_smses,
                    'default_attribute' => $default_attribute,
                ]);
                break;

            case 'operator':
                return view('admins.operator.sms-events', [
                    'event_smses' => $event_smses,
                    'default_attribute' => $default_attribute,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.sms-events', [
                    'event_smses' => $event_smses,
                    'default_attribute' => $default_attribute,
                ]);
                break;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\event_sms  $event_sms
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, event_sms $event_sms)
    {
        $this->authorize('view', $event_sms);

        $operator = $request->user();
        $lang_code = strlen($operator->lang_code) ? $operator->lang_code : config('consumer.lang_code');
        $default_attribute = 'default_sms_' . $lang_code;

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.sms-event-show', [
                    'event_sms' => $event_sms,
                    'default_attribute' => $default_attribute,
                ]);
                break;

            case 'operator':
                return view('admins.operator.sms-event-show', [
                    'event_sms' => $event_sms,
                    'default_attribute' => $default_attribute,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.sms-event-show', [
                    'event_sms' => $event_sms,
                    'default_attribute' => $default_attribute,
                ]);
                break;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\event_sms  $event_sms
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, event_sms $event_sms)
    {
        $this->authorize('update', $event_sms);

        $operator = $request->user();

        // copy to self
        if ($event_sms->operator_id == 0) {

            $event_sms = $event_sms->replicate()->fill([
                'operator_id' => $operator->id,
            ]);

            $event_sms->save();

            return redirect()->route('event_sms.edit', ['event_sms' => $event_sms]);
        }

        $lang_code = strlen($operator->lang_code) ? $operator->lang_code : config('consumer.lang_code');
        $default_attribute = 'default_sms_' . $lang_code;

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.sms-event-edit', [
                    'event_sms' => $event_sms,
                    'default_attribute' => $default_attribute,
                ]);
                break;

            case 'operator':
                return view('admins.operator.sms-event-edit', [
                    'event_sms' => $event_sms,
                    'default_attribute' => $default_attribute,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.sms-event-edit', [
                    'event_sms' => $event_sms,
                    'default_attribute' => $default_attribute,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\event_sms  $event_sms
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, event_sms $event_sms)
    {
        $this->authorize('update', $event_sms);

        // status
        if ($request->filled('status')) {
            $request->validate([
                'status' => 'in:enabled,disabled',
            ]);
            $event_sms->status = $request->status;
            $event_sms->save();
        }

        // operator_sms
        if ($request->filled('operator_sms')) {
            $event_sms->operator_sms = $request->operator_sms;
            $event_sms->save();
        }

        // return
        return redirect()->route('event_sms.index')->with('success', 'Updated Successfully!');
    }
}
