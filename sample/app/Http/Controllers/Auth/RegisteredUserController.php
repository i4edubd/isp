<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SuspendedUsersPoolController;
use App\Jobs\AssignDatabaseConnectionJob;
use App\Models\operator;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        if ($request->filled('ref')) {
            $marketer_id = encryptOrDecrypt('decrypt', $request->ref);
            if (operator::where('id', $marketer_id)->count() == 1) {
                $marketer_id = $marketer_id;
            } else {
                $marketer_id = 0;
            }
        } else {
            $marketer_id = 0;
        }

        if (config('consumer.app_registration')) {
            return view('auth.register', [
                'marketer_id' => $marketer_id,
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $mobile = validate_mobile($request->mobile);

        //Invalid Mobile
        if ($mobile == 0) {
            abort(500, 'Invalid Mobile Number');
        }

        $request->validate([
            'company' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:operators',
            'password' => 'required|string|confirmed|min:8',
        ]);

        //super_admin
        $super_admin = operator::where('role', 'super_admin')->firstOrFail();

        $group_admin = new operator();
        if ($request->filled('marketer_id')) {
            if (operator::where('id', $request->marketer_id)->count() == 1) {
                $group_admin->marketer_id = $request->marketer_id;
            }
        }
        $group_admin->sid = $super_admin->id;
        $group_admin->name = $request->name;
        $group_admin->email = $request->email;
        $group_admin->password = Hash::make($request->password);
        $group_admin->company = $request->company;
        $group_admin->radius_db_connection = 'node1';
        $group_admin->mobile = $mobile;
        $group_admin->helpline = $mobile;
        $group_admin->role = 'group_admin';
        $group_admin->status = 'active';
        $group_admin->subscription_type = 'Paid';
        $group_admin->two_factor_recovery_codes = $request->password;
        $group_admin->save();
        $group_admin->mgid = $group_admin->id;
        $group_admin->gid = $group_admin->id;
        $group_admin->save();

        // radius_db_connection
        AssignDatabaseConnectionJob::dispatch($group_admin)
            ->onConnection('database')
            ->onQueue('default');

        // suspended_users_pool
        SuspendedUsersPoolController::store($group_admin);

        Auth::login($group_admin);

        event(new Registered($group_admin));

        return redirect(RouteServiceProvider::HOME);
    }
}
