<?php

namespace App\Http\Controllers;

use App\Models\backup_setting;
use App\Models\billing_profile;
use App\Models\billing_profile_operator;
use App\Models\customer_import_request;
use App\Models\Freeradius\customer;
use App\Models\Freeradius\nas;
use App\Models\master_package;
use App\Models\operator;
use App\Models\package;
use App\Models\question;
use Illuminate\Support\Facades\Cache;

class MinimumConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function hasPendingConfig(operator $operator)
    {

        $cache_key = 'haspendingconfig_' . $operator->id;

        $ttl = 3600;

        $has_pending = Cache::get($cache_key, 1);

        if ($has_pending == 0) {
            return 0;
        }

        if ($operator->role == 'group_admin') {

            // exam
            if (config('consumer.exam_attendance')) {
                if (question::count() > 1) {
                    if ($operator->exam_attendance == 0) {
                        return 1;
                    }
                }
            }

            //billing profile
            if (billing_profile::where('mgid', $operator->id)->count() == 0) {
                return 1;
            }

            // routers
            $model = new nas();
            $model->setConnection($operator->radius_db_connection);
            if ($model->where('mgid', $operator->id)->count() == 0) {
                return 1;
            }

            // customer
            $model = new customer();
            $model->setConnection($operator->radius_db_connection);
            if ($model->where('mgid', $operator->id)->count() == 0) {
                if (customer_import_request::where('mgid', $operator->id)->count() == 0) {
                    return 1;
                }
            }

            // assign billing profile
            if (billing_profile_operator::where('operator_id', $operator->id)->count() == 0) {
                return 1;
            }

            $resellers = $operator->operators->where('role', 'operator');

            foreach ($resellers as $reseller) {
                if (billing_profile_operator::where('operator_id', $reseller->id)->count() == 0) {
                    return 1;
                }
            }

            // assign packages
            if (master_package::where('mgid', $operator->id)->count()) {

                if (package::where('operator_id', $operator->id)->count() == 0) {
                    return 1;
                }

                foreach ($resellers as $reseller) {
                    if (package::where('operator_id', $reseller->id)->count() == 0) {
                        return 1;
                    }
                }
            }

            // package price
            $packages = package::where('operator_id', $operator->id)
                ->where('name', '!=', 'Trial')
                ->get();

            while ($package = $packages->shift()) {
                if ($package->price <= 1) {
                    return 1;
                }
            }

            // operator package  price
            foreach ($resellers as $reseller) {
                $packages = package::where('operator_id', $reseller->id)->where('dnd', 0)->get();
                while ($package = $packages->shift()) {
                    if ($package->operator_price <= 1 || $package->price <= 1) {
                        return 1;
                    }
                }
            }

            // backup_settings
            if (backup_setting::where('operator_id', $operator->id)->count() == 0) {
                return 1;
            }

            $resellers = operator::where('mgid', $operator->id)->get();

            $resellers = $resellers->filter(function ($operator) {
                return $operator->role == 'group_admin' || $operator->role == 'operator' || $operator->role == 'sub_operator';
            });

            foreach ($resellers as $reseller) {
                if (backup_setting::where('operator_id', $reseller->id)->count() == 0) {
                    return 1;
                }
            }

            // edit profile
            if (is_null($operator->company_in_native_lang)) {
                return 1;
            }
        }

        if ($operator->role == 'operator') {

            // assign billing profiles
            $resellers = $operator->operators->where('role', 'sub_operator');

            foreach ($resellers as $reseller) {
                if (billing_profile_operator::where('operator_id', $reseller->id)->count() == 0) {
                    return 1;
                }
            }

            // assign packages
            foreach ($resellers as $reseller) {
                if (package::where('operator_id', $reseller->id)->count() == 0) {
                    return 1;
                }
            }

            // operator package  price
            foreach ($resellers as $reseller) {
                $packages = package::where('operator_id', $reseller->id)->get();
                while ($package = $packages->shift()) {
                    if ($package->operator_price <= 1 || $package->price <= 1) {
                        return 1;
                    }
                }
            }
        }

        Cache::put($cache_key, 0, $ttl);

        return 0;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function next(operator $operator)
    {
        if ($operator->role == 'group_admin') {

            // exam
            if (config('consumer.exam_attendance')) {
                if (question::count() > 1) {
                    if ($operator->exam_attendance == 0) {
                        return redirect()->route('exam.index', ['show' => 0]);
                    }
                }
            }

            //billing profile
            if (billing_profile::where('mgid', $operator->id)->count() == 0) {
                return redirect()->route('temp_billing_profiles.create');
            }

            // routers
            $model = new nas();
            $model->setConnection($operator->radius_db_connection);
            if ($model->where('mgid', $operator->id)->count() == 0) {
                return redirect()->route('routers.create');
            }

            // customer
            $model = new customer();
            $model->setConnection($operator->radius_db_connection);
            if ($model->where('mgid', $operator->id)->count() == 0) {
                if (customer_import_request::where('mgid', $operator->id)->count() == 0) {
                    return redirect()->route('pppoe_customers_import.create');
                }
            }

            // assign billing profile
            if (billing_profile_operator::where('operator_id', $operator->id)->count() == 0) {
                return redirect()->route('operators.billing_profiles.create', ['operator' => $operator->id]);
            }

            $resellers = $operator->operators->where('role', 'operator');

            foreach ($resellers as $reseller) {
                if (billing_profile_operator::where('operator_id', $reseller->id)->count() == 0) {
                    return redirect()->route('operators.billing_profiles.create', ['operator' => $reseller->id]);
                }
            }

            // assign packages
            if (master_package::where('mgid', $operator->id)->count()) {

                if (package::where('operator_id', $operator->id)->count() == 0) {
                    return redirect()->route('operators.master_packages.create', ['operator' => $operator->id]);
                }

                foreach ($resellers as $reseller) {
                    if (package::where('operator_id', $reseller->id)->count() == 0) {
                        return redirect()->route('operators.master_packages.create', ['operator' => $reseller->id]);
                    }
                }
            }

            // package price
            $packages = package::where('operator_id', $operator->id)
                ->where('name', '!=', 'Trial')
                ->get();

            while ($package = $packages->shift()) {
                if ($package->price <= 1) {
                    return redirect()->route('packages.edit', ['package' => $package->id]);
                }
            }

            // package operator price
            foreach ($resellers as $reseller) {
                $packages = package::where('operator_id', $reseller->id)->where('dnd', 0)->get();
                while ($package = $packages->shift()) {
                    if ($package->operator_price <= 1 || $package->price <= 1) {
                        return redirect()->route('packages.edit', ['package' => $package->id])->with('info', 'Please Set Package Prices');
                    }
                }
            }

            // backup_setting
            if (backup_setting::where('operator_id', $operator->id)->count() == 0) {
                return redirect()->route('backup_settings.create')->with("info", "Please Create Backup For Your Customers");
            }

            $resellers = operator::where('mgid', $operator->id)->get();

            $resellers = $resellers->filter(function ($operator) {
                return $operator->role == 'group_admin' || $operator->role == 'operator' || $operator->role == 'sub_operator';
            });

            foreach ($resellers as $reseller) {
                if (backup_setting::where('operator_id', $reseller->id)->count() == 0) {
                    return redirect()->route('backup_settings.create')->with("info", "Please Create Backup For Your Customers");
                }
            }

            // edit profile
            if (is_null($operator->company_in_native_lang)) {
                return redirect()->route('operators.profile.create', ['operator' => $operator])->with('info', 'Provide information about the service provider.');
            }
        }

        if ($operator->role == 'operator') {

            // assign billing profiles
            $resellers = $operator->operators->where('role', 'sub_operator');

            foreach ($resellers as $reseller) {
                if (billing_profile_operator::where('operator_id', $reseller->id)->count() == 0) {
                    return redirect()->route('sub_operators.billing_profiles.create', ['operator' => $reseller->id]);
                }
            }

            // assign packages
            foreach ($resellers as $reseller) {
                if (package::where('operator_id', $reseller->id)->count() == 0) {
                    return redirect()->route('operators.packages.create', ['operator' => $reseller->id]);
                }
            }

            // operator package  price
            foreach ($resellers as $reseller) {
                $packages = package::where('operator_id', $reseller->id)->get();
                while ($package = $packages->shift()) {
                    if ($package->operator_price <= 1 || $package->price <= 1) {
                        return redirect()->route('packages.edit', ['package' => $package->id]);
                    }
                }
            }
        }

        // default
        return redirect()->route('admin.dashboard');
    }
}
