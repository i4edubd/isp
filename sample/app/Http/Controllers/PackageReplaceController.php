<?php

namespace App\Http\Controllers;

use App\Jobs\PackageReplaceJob;
use App\Models\operator;
use App\Models\package;
use App\Models\pppoe_profile;
use Illuminate\Http\Request;

class PackageReplaceController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\package  $package
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, package $package)
    {
        $this->authorize('replace', $package);

        $admin = $request->user();

        $operator = operator::find($package->operator_id);

        $connection_type = $package->master_package->connection_type;

        $operator_packages = $operator->packages;

        $operator_packages = $operator_packages->except($package->id);

        $other_packages = $operator_packages->filter(function ($operator_package) use ($connection_type) {
            return $operator_package->master_package->connection_type == $connection_type;
        });

        switch ($admin->role) {
            case 'group_admin':
                return view('admins.group_admin.package-replace', [
                    'package' => $package,
                    'packages' => $other_packages,
                ]);
                break;

            case 'operator':
                return view('admins.operator.package-replace', [
                    'package' => $package,
                    'packages' => $other_packages,
                ]);
                break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\package  $package
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, package $package)
    {
        $request->validate([
            'package_id' => 'required',
        ]);

        if ($request->package_id == $package->id) {
            return redirect()->route('packages.index');
        }

        $new_package = package::findOrFail($request->package_id);

        $message = self::replace($package, $new_package);

        if ($request->user()->role == 'group_admin') {
            if ($request->user()->id !== $package->operator_id) {
                return redirect()->route('operators.master_packages.index', ['operator' => $package->operator_id])->with('success', $message);
            }
        }

        return redirect()->route('packages.index')->with('success', $message);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\package  $package
     * @param  \App\Models\package  $new_package
     * @return \Illuminate\Http\Response
     */
    public static function replace(package $package, package $new_package)
    {
        // type Error
        if ($new_package->master_package->connection_type !== $package->master_package->connection_type) {
            return 'connection type not same';
        }

        // Space Error
        if ($package->master_package->connection_type == 'PPPoE') {

            if ($package->master_package->pppoe_profile_id !== $new_package->master_package->pppoe_profile_id) {

                $pppoe_profile = pppoe_profile::findOrFail($package->master_package->pppoe_profile_id);

                $new_profile = pppoe_profile::findOrFail($new_package->master_package->pppoe_profile_id);

                $ipv4pool = $pppoe_profile->ipv4pool;

                $new_pool = $new_profile->ipv4pool;

                if ($ipv4pool->id !== $new_pool->id) {

                    $required_space = $ipv4pool->used_space;

                    $free_space = $new_pool->broadcast - $new_pool->gateway - $new_pool->used_space;

                    if ($required_space > $free_space) {
                        return 'not enough ipv4 address to replace';
                    }
                }
            }
        }

        $package->job_processing = 1;
        $package->save();

        PackageReplaceJob::dispatch($package, $new_package)
            ->onConnection('database')
            ->onQueue('package_replace');

        return 'success';
    }
}
