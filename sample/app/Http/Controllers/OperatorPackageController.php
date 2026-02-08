<?php

namespace App\Http\Controllers;

use App\Models\operator;
use App\Models\package;
use Illuminate\Http\Request;

class OperatorPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function index(operator $operator)
    {
        $this->authorize('assignPackages', $operator);

        return view('admins.operator.operator-packages', [
            'operator' => $operator,
            'packages' => $operator->packages,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, operator $operator)
    {
        if ($request->user()->role !== 'operator') {
            abort(403);
        }

        $this->authorize('assignPackages', $operator);

        $packages = package::where('operator_id', $request->user()->id)->get();

        $packages = $packages->filter(function ($package) use ($operator) {
            $assigned_where = [
                ['operator_id', '=', $operator->id],
                ['mpid', '=', $package->mpid],
            ];

            if (package::where($assigned_where)->count()) {
                return false;
            } else {
                return true;
            }
        });

        if ($packages->count() == 0) {
            return redirect(url()->previous())->with('success', 'All Packages were assigned!');
        }

        return view('admins.operator.operator-packages-create', [
            'operator' => $operator,
            'packages' => $packages,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\operator  $operator
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, operator $operator)
    {
        $this->authorize('assignPackages', $operator);

        $request->validate([
            'package_id' => 'numeric|required',
        ]);

        return redirect()->route('operators.packages.edit', ['package' => $request->package_id, 'operator' => $operator->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\package  $package
     * @return \Illuminate\Http\Response
     */
    public function edit(operator $operator, package $package)
    {
        $this->authorize('assignPackages', $operator);

        return view('admins.operator.operator-packages-edit', [
            'package' => $package,
            'operator' => $operator,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\operator  $operator
     * @param  \App\Models\package  $package
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, operator $operator, package $package)
    {
        $this->authorize('assignPackages', $operator);

        if ($request->name == 'Trial') {
            return redirect()->route('operators.packages.edit', ['operator' => $operator->id, 'package' => $package->id])->with('error', 'Trial package cannot be created!');
        }

        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'operator_price' => 'required|numeric',
            'visibility' => 'required|in:public,private',
        ]);

        if ($request->operator_price > $request->price) {
            return redirect()->route('operators.packages.edit', ['operator' => $operator->id, 'package' => $package->id])->with('error', 'package price must be greater than operator price!');
        }

        // duplicate
        $duplicate_where = [
            ['operator_id', '=', $operator->id],
            ['mpid', '=', $package->mpid],
        ];

        if (package::where($duplicate_where)->count() == 0) {
            $sub_package = new package();
            $sub_package->mgid = $request->user()->mgid;
            $sub_package->gid = $request->user()->id;
            $sub_package->operator_id = $operator->id;
            $sub_package->mpid = $package->mpid;
            $sub_package->ppid = $package->id;
            $sub_package->name = $request->name;
            $sub_package->price = $request->price;
            $sub_package->operator_price = $request->operator_price;
            $sub_package->visibility = $request->visibility;
            $sub_package->save();
        }

        if (MinimumConfigurationController::hasPendingConfig($request->user())) {
            return redirect()->route('configuration.next', ['operator' => $request->user()->id]);
        }

        return redirect()->route('operators.packages.index', ['operator' => $operator->id])->with('success', 'The package added successfully!');
    }
}
