<?php

namespace App\Http\Controllers;

use App\Models\event_sms;
use App\Models\expiration_notifier;
use Illuminate\Http\Request;

class ExpirationNotifierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $operator = $request->user();
        $expiration_notifier = expiration_notifier::where('operator_id', $operator->id)->firstOr(function () use ($operator) {
            $expiration_notifier = new expiration_notifier();
            $expiration_notifier->operator_id = $operator->id;
            $expiration_notifier->status = 'inactive';
            $expiration_notifier->connection_types = json_encode(['PPPoE', 'Hotspot']);
            $expiration_notifier->billing_types = json_encode(['Daily']);
            $expiration_notifier->notify_before = 1;
            $expiration_notifier->unit = 'Day';
            $expiration_notifier->save();
            return $expiration_notifier;
        });

        $event_sms = event_sms::where('event', 'EXPIRATION_NOTIFICATION')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'EXPIRATION_NOTIFICATION')
                    ->where('operator_id', 0)
                    ->first();
            });
        $message = SmsGenerator::getSMS($operator, $event_sms);

        return match ($operator->role) {
            'group_admin' => view('admins.group_admin.expiration_notifier_index', ['expiration_notifier' => $expiration_notifier, 'message' => $message]),
            'operator' => view('admins.operator.expiration_notifier_index', ['expiration_notifier' => $expiration_notifier, 'message' => $message]),
            'sub_operator' => view('admins.sub_operator.expiration_notifier_index', ['expiration_notifier' => $expiration_notifier, 'message' => $message]),
        };
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\expiration_notifier  $expiration_notifier
     * @return \Illuminate\Http\Response
     */
    public function edit(expiration_notifier $expiration_notifier)
    {
        $this->authorize('update', $expiration_notifier);

        $operator = $expiration_notifier->operator;
        $connection_types = ['PPPoE', 'Hotspot', 'StaticIp', 'Other'];
        $billing_types =  ['Daily', 'Monthly'];
        $checked_connection_types = json_decode($expiration_notifier->connection_types, true);
        $checked_billing_types = json_decode($expiration_notifier->billing_types, true);
        $unchecked_connection_types = array_diff($connection_types, $checked_connection_types);
        $unchecked_billing_types = array_diff($billing_types, $checked_billing_types);
        $event_sms = event_sms::where('event', 'EXPIRATION_NOTIFICATION')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'EXPIRATION_NOTIFICATION')
                    ->where('operator_id', 0)
                    ->first();
            });
        $message = SmsGenerator::getSMS($operator, $event_sms);

        return match ($operator->role) {
            'group_admin' => view('admins.group_admin.expiration_notifier_edit', [
                'expiration_notifier' => $expiration_notifier,
                'checked_connection_types' => $checked_connection_types,
                'checked_billing_types' => $checked_billing_types,
                'unchecked_connection_types' => $unchecked_connection_types,
                'unchecked_billing_types' => $unchecked_billing_types,
                'message' => $message
            ]),
            'operator' => view('admins.operator.expiration_notifier_edit', [
                'expiration_notifier' => $expiration_notifier,
                'checked_connection_types' => $checked_connection_types,
                'checked_billing_types' => $checked_billing_types,
                'unchecked_connection_types' => $unchecked_connection_types,
                'unchecked_billing_types' => $unchecked_billing_types,
                'message' => $message
            ]),
            'sub_operator' => view('admins.sub_operator.expiration_notifier_edit', [
                'expiration_notifier' => $expiration_notifier,
                'checked_connection_types' => $checked_connection_types,
                'checked_billing_types' => $checked_billing_types,
                'unchecked_connection_types' => $unchecked_connection_types,
                'unchecked_billing_types' => $unchecked_billing_types,
                'message' => $message
            ]),
        };
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\expiration_notifier  $expiration_notifier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, expiration_notifier $expiration_notifier)
    {
        $this->authorize('update', $expiration_notifier);

        $request->validate([
            'status' => 'required|in:active,inactive',
            'notify_before' => 'required|numeric|min:1',
            'message' => 'required|string',
        ]);

        $connection_types = ['PPPoE', 'Hotspot', 'StaticIp', 'Other'];
        $billing_types =  ['Daily', 'Monthly'];
        $checked_connection_types = [];
        $checked_billing_types = [];

        foreach ($connection_types as $connection_type) {
            if ($request->filled($connection_type)) {
                $checked_connection_types[] = $connection_type;
            }
        }

        foreach ($billing_types as $billing_type) {
            if ($request->filled($billing_type)) {
                $checked_billing_types[] = $billing_type;
            }
        }

        if (count($checked_connection_types) == 0 || count($checked_billing_types) == 0) {
            return redirect()->route('expiration_notifiers.edit', ['expiration_notifier' => $expiration_notifier])->with('info', 'At least one connection type and one billing type are needed.');
        }

        $operator = $expiration_notifier->operator;
        $event_sms = event_sms::where('event', 'EXPIRATION_NOTIFICATION')
            ->where('operator_id', $operator->id)
            ->firstOr(function () {
                return event_sms::where('event', 'EXPIRATION_NOTIFICATION')
                    ->where('operator_id', 0)
                    ->first();
            });
        $message = SmsGenerator::getSMS($operator, $event_sms);
        if ($message != $request->message) {
            event_sms::updateOrCreate(
                [
                    'operator_id' => $operator->id,
                    'event' => 'EXPIRATION_NOTIFICATION'
                ],
                [
                    'readable_event' => 'Expiration Notification',
                    'variables' =>  '[EXPIRATION_DATE],[COMPANY_NAME],[HELPLINE]',
                    'default_sms' => 'Dear Sir, Your account will expire on [EXPIRATION_DATE]  -  [COMPANY_NAME] . For any query please call : [HELPLINE]',
                    'default_sms_bn' => 'স্যার, আপনার অ্যাকাউন্টের মেয়াদ [EXPIRATION_DATE] তারিখে শেষ হবে - [COMPANY_NAME] । প্রয়োজনে কল করুনঃ [HELPLINE]',
                    'status' => 'enabled',
                    'operator_sms' => $request->message,
                ]
            );
        }

        $expiration_notifier->status = $request->status;
        $expiration_notifier->connection_types = json_encode($checked_connection_types);
        $expiration_notifier->billing_types = json_encode($checked_billing_types);
        $expiration_notifier->notify_before = $request->notify_before;
        $expiration_notifier->save();

        return redirect()->route('expiration_notifiers.index')->with('info', 'Expiration Notification Setting Updated');
    }
}
