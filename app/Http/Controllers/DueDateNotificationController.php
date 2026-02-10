<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Sms\SmsGatewayController;
use App\Jobs\NotifyDueDates;
use App\Models\due_date_reminder;
use App\Models\Freeradius\customer;
use App\Models\operator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DueDateNotificationController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\due_date_reminder  $due_date_reminder
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, due_date_reminder $due_date_reminder)
    {
        $operator = $request->user();

        // << validation
        if ($operator->id !== $due_date_reminder->operator_id) {
            abort(403);
        }

        $now = Carbon::createFromFormat(config('app.date_format'), date(config('app.date_format')));

        $expiration = Carbon::createFromFormat(config('app.date_format'), $due_date_reminder->expiration_date);

        if ($expiration->lessThan($now)) {
            return redirect()->route('due_date_reminders.index')->with('error', 'Expiration Date is Over!');
        }
        // >>

        $customers = customer::where('operator_id', $operator->id)->get();

        $date = $due_date_reminder->expiration_date;

        $customers = $customers->filter(function ($customer) use ($date, $operator) {
            $expired_at = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($operator->id), 'en')->format(config('app.date_format'));
            return $expired_at == $date &&
                $customer->payment_status == 'billed' &&
                ($customer->status == 'active' || $customer->status == 'fup');
        });

        $customers_count = $customers->count();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.due-date-notification', [
                    'due_date_reminder' => $due_date_reminder,
                    'customers_count' => $customers_count,
                ]);
                break;

            case 'operator':
                return view('admins.operator.due-date-notification', [
                    'due_date_reminder' => $due_date_reminder,
                    'customers_count' => $customers_count,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.due-date-notification', [
                    'due_date_reminder' => $due_date_reminder,
                    'customers_count' => $customers_count,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\due_date_reminder  $due_date_reminder
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, due_date_reminder $due_date_reminder)
    {
        $operator = $request->user();

        // << validation
        if ($operator->id !== $due_date_reminder->operator_id) {
            abort(403);
        }

        $now = Carbon::createFromFormat(config('app.date_format'), date(config('app.date_format')), getTimeZone($operator->id));

        $expiration = Carbon::createFromFormat(config('app.date_format'), $due_date_reminder->expiration_date, getTimeZone($operator->id));

        if ($expiration->lessThan($now)) {
            return redirect()->route('due_date_reminders.index')->with('error', 'Expiration Date is Over!');
        }
        // >>

        $connection = config('app.env') == 'production' ? 'redis' : 'database';
        NotifyDueDates::dispatch($due_date_reminder)
            ->onConnection($connection)
            ->onQueue('notify_due_dates');

        return redirect()->route('due_date_reminders.index')->with('success', 'Job is processing');
    }


    /**
     * Push Notifications.
     *
     * @param  \App\Models\due_date_reminder  $due_date_reminder
     * @return void
     */
    public static function pushNotifications(due_date_reminder $due_date_reminder)
    {
        $operator = operator::find($due_date_reminder->operator_id);
        $country_code = getCountryCode($operator->id);

        $model = new customer();
        $model->setConnection($operator->node_connection);
        $customers = $model->where('operator_id', $operator->id)->get();

        $date = $due_date_reminder->expiration_date;

        $customers = $customers->filter(function ($customer) use ($date, $operator) {
            $expired_at = Carbon::createFromIsoFormat(config('app.expiry_time_format'), $customer->package_expired_at, getTimeZone($operator->id), 'en')->format(config('app.date_format'));
            return $expired_at == $date &&
                $customer->payment_status == 'billed' &&
                ($customer->status == 'active' || $customer->status == 'fup');
        });

        $customers_count = $customers->count();

        while ($customer = $customers->shift()) {
            $mobile = validate_mobile($customer->mobile, $country_code);
            if ($mobile == 0) {
                continue;
            }

            $message = SmsGenerator::dueReminderMsg($operator, $due_date_reminder, $customer);

            SmsGatewayController::sendSms($operator, $mobile, $message, $customer->id);
        }

        if ($customers_count) {
            $mobile = validate_mobile($operator->mobile, $country_code);
            if ($mobile) {
                $message = SmsGenerator::confirmationSms($operator, $date, $customers_count);
                $sms_gateway = new SmsGatewayController();
                $sms_gateway->sendSms($operator, $mobile, $message);
            }
        }
    }
}
