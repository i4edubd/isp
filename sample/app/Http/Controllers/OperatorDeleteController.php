<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customer\CustomerController;
use App\Jobs\GroupAdminDeleteJob;
use App\Jobs\OperatorDeleteJob;
use App\Models\event_sms;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\nas;
use App\Models\operator;
use App\Models\pgsql_activity_log;
use Illuminate\Http\Request;

class OperatorDeleteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->role !== 'developer') {
            abort(500);
        }

        $filter = [];

        $filter[] = ['role', '=', 'group_admin'];

        if ($request->filled('sp_request')) {

            $filter[] = ['sp_request', '=', $request->sp_request];
        }

        if ($request->filled('sd_request')) {

            $filter[] = ['sd_request', '=', $request->sd_request];
        }

        if ($request->filled('mrk_email_count')) {

            $filter[] = ['mrk_email_count', '=', $request->mrk_email_count];
        }

        $operators = operator::where($filter)
            ->orderBy('provisioning_status', 'asc')
            ->get();

        return view('admins.developer.operators-delete', [
            'operators' => $operators,
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function show(operator $operator)
    {
        return view('admins.developer.operators-show', [
            'operator' => $operator
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, operator $operator)
    {
        if ($request->user()->role !== 'developer') {
            abort(500);
        }

        $delete = 0;

        if ($operator->role == 'group_admin') {
            $delete = 1;
        }

        if ($operator->role == 'operator') {
            $delete = 1;
        }

        if ($delete == 0) {
            abort(500);
        }

        if ($operator->role == 'group_admin') {
            pgsql_activity_log::create([
                'gid' => $request->user()->gid,
                'operator_id' => $request->user()->id,
                'customer_id' => $operator->id,
                'topic' => 'destroy_group_admin',
                'year' => date(config('app.year_format')),
                'month' => date(config('app.month_format')),
                'week' => date(config('app.week_format')),
                'log' => $request->user()->name . ' has deleted group_admin: ' . $operator->name,
            ]);

            $operator->deleting = 1;
            $operator->save();

            GroupAdminDeleteJob::dispatch($operator)
                ->onConnection('database')
                ->onQueue('default');
        }

        if ($operator->role == 'operator') {
            pgsql_activity_log::create([
                'gid' => $request->user()->gid,
                'operator_id' => $request->user()->id,
                'customer_id' => $operator->id,
                'topic' => 'destroy_operator',
                'year' => date(config('app.year_format')),
                'month' => date(config('app.month_format')),
                'week' => date(config('app.week_format')),
                'log' => $request->user()->name . ' has deleted operator: ' . $operator->name,
            ]);

            $operator->deleting = 1;
            $operator->save();

            OperatorDeleteJob::dispatch($operator)
                ->onConnection('database')
                ->onQueue('default');
        }

        return redirect()->route('operators-delete.index')->with('success', 'Job is processing!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\operator  $operator
     * @return void
     */
    public static function deleteGroupAdmin(operator $operator)
    {

        if ($operator->role !== 'group_admin') {
            return 0;
        }

        // delete operators
        $operators_where = [
            ['gid', '=', $operator->id],
            ['role', '=', 'operator'],
        ];

        $operators = operator::where($operators_where)->get();

        foreach ($operators as $seleted_operator) {
            self::deleteOperator($seleted_operator);
        }

        // delete routers
        $model = new nas();
        $model->setConnection($operator->radius_db_connection);
        $model->where('mgid', $operator->id)->delete();

        // delete the group admin
        self::deleteOperator($operator);

        return 0;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\operator  $operator
     * @return void
     */
    public static function deleteOperator(operator $operator)
    {
        //deleting
        $operator->deleting = 1;
        $operator->save();

        //delete customers
        $model = new customer();
        $model->setConnection($operator->radius_db_connection);
        $customers = $model->where('operator_id', $operator->id)->get();

        foreach ($customers as $customer) {
            $controller = new CustomerController();
            $controller->destroy($customer);
        }

        // delete sub_operators
        $sub_operator_where = [
            ['gid', '=', $operator->id],
            ['role', '=', 'sub_operator'],
        ];

        $sub_operators = operator::where($sub_operator_where)->get();

        foreach ($sub_operators as $sub_operator) {
            self::deleteSubOperator($sub_operator);
        }

        // delete managers
        $managers_where = [
            ['gid', '=', $operator->id],
            ['role', '=', 'manager'],
        ];

        $managers = operator::where($managers_where)->get();

        foreach ($managers as $manager) {
            // yearly Summary
            YearlySummaryDeleteController::operatorSummary($manager);
            $manager->delete();
        }

        // yearly summary
        YearlySummaryDeleteController::operatorSummary($operator);


        // event_sms
        event_sms::where('operator_id', $operator->id)->delete();

        //delete operator
        $operator->delete();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\operator  $operator
     * @return void
     */
    public static function deleteSubOperator(operator $operator)
    {
        // deleting
        $operator->deleting = 1;
        $operator->save();

        //delete customers
        $model = new customer();
        $model->setConnection($operator->radius_db_connection);
        $customers = $model->where('operator_id', $operator->id)->get();

        foreach ($customers as $customer) {
            $controller = new CustomerController();
            $controller->destroy($customer);
        }

        // delete managers
        $managers_where = [
            ['gid', '=', $operator->id],
            ['role', '=', 'manager'],
        ];

        $managers = operator::where($managers_where)->get();

        foreach ($managers as $manager) {
            // yearly summary
            YearlySummaryDeleteController::operatorSummary($manager);
            $manager->delete();
        }

        // yearly summary
        YearlySummaryDeleteController::operatorSummary($operator);

        // event_sms
        event_sms::where('operator_id', $operator->id)->delete();

        //delete operator
        $operator->delete();
    }
}
