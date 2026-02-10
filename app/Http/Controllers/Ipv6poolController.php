<?php

namespace App\Http\Controllers;

use App\Models\ipv6pool;
use App\Models\operator;
use Illuminate\Http\Request;
use Net_IPv6;

class Ipv6poolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pools = ipv6pool::where('mgid', $request->user()->id)->get();

        return view('admins.group_admin.ipv6pools', [
            'pools' => $pools,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admins.group_admin.ipv6pools-create');
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
            'name' => 'required',
            'prefix' => 'required',
        ]);

        $name = getVarName($request->name);

        $where = [
            ['mgid', '=', $request->user()->id],
            ['name', '=', $name],
        ];

        if (ipv6pool::where($where)->count()) {
            return redirect()->route('ipv6pools.create')->with('error', 'Duplicate IPv6 Pool Name');
        }

        $prefix = $request->prefix;

        $ipv6lib = new Net_IPv6();

        //checkIPv6
        $validIPv6 = $ipv6lib->checkIPv6($prefix);

        if ($validIPv6 == false) {
            return redirect()->route('ipv6pools.create')->with('error', 'Invalid Prefix');
        }

        //check prefix length
        $prefix_length = $ipv6lib->getNetmaskSpec($prefix);

        if ($prefix_length > 48 || $prefix_length < 32) {
            return redirect()->route('ipv6pools.create')->with('error', 'Please use prefix length/netmask between 32 and 48');
        }

        //check dumplicate
        $address = $ipv6lib->parseAddress($prefix);

        $lowest_address = $address['start'];

        $highest_address = $address['end'];

        $overlapped = $this->isOverlapped($request->user(), $lowest_address, $highest_address);

        if ($overlapped) {
            return redirect()->route('ipv6pools.create')->with('error', 'Subnet overlapped with: ' . $overlapped);
        }

        $ipv6pool = new ipv6pool();
        $ipv6pool->mgid = $request->user()->id;
        $ipv6pool->name = $name;
        $ipv6pool->prefix = $ipv6lib->compress($ipv6lib->getNetmask($prefix) . '/' . $ipv6lib->getNetmaskSpec($prefix));
        $ipv6pool->lowest_address = $lowest_address;
        $ipv6pool->highest_address = $highest_address;
        $ipv6pool->save();

        return redirect()->route('ipv6pools.index')->with('success', 'IPv6Pool Created Successfully');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ipv6pool  $ipv6pool
     * @return \Illuminate\Http\Response
     */
    public function destroy(ipv6pool $ipv6pool)
    {
        $this->authorize('delete', $ipv6pool);

        $ipv6pool->delete();

        return redirect()->route('ipv6pools.index')->with('success', 'The IPv6 Pool has been deleted successfully!');
    }


    /**
     * Check duplicate IPv6Poll Name.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $name
     * @return \Illuminate\View\View
     */
    public function checkDuplicateName(Request $request, string $name)
    {
        $name = getVarName($name);

        $where = [
            ['mgid', '=', $request->user()->id],
            ['name', '=', $name],
        ];

        if (ipv6pool::where($where)->count()) {
            $duplicate = 1;
        } else {
            $duplicate = 0;
        }

        return view('laraview.layouts.duplicate-check-response', [
            'duplicate' => $duplicate,
        ]);
    }


    /**
     * Check IPv6 Prefix Error.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $name
     * @return \Illuminate\View\View
     */
    public function checkPrefixError(Request $request)
    {
        $prefix = $request->prefix;

        $ipv6lib = new Net_IPv6();

        //checkIPv6
        $validIPv6 = $ipv6lib->checkIPv6($prefix);

        if ($validIPv6 == false) {
            return '<span class="text-danger">Invalid Prefix</span>';
        }

        //check prefix length
        $prefix_length = $ipv6lib->getNetmaskSpec($prefix);

        if ($prefix_length > 48 || $prefix_length < 32) {
            return '<span class="text-danger">Please use prefix length/netmask between 32 and 48</span>';
        }

        //check dumplicate
        $address = $ipv6lib->parseAddress($prefix);

        $lowest_address = $address['start'];

        $highest_address = $address['end'];

        $overlapped = $this->isOverlapped($request->user(), $lowest_address, $highest_address);

        if ($overlapped) {
            return '<span class="text-danger">Subnet overlapped with: </span>' . $overlapped;
        }

        return view('laraview.layouts.duplicate-check-response', [
            'duplicate' => 0,
        ]);
    }


    /**
     * Check IPv4 Subnet Overlapping.
     *
     * @param  \App\Models\operator  $operator
     * @param  string  $lowest_address
     * @param  string  $highest_address
     * @return  string subnet || bool 0
     */
    public static function isOverlapped(operator $operator, string $lowest_address, string $highest_address)
    {

        $ipv6lib = new Net_IPv6();
        $pools = ipv6pool::where('mgid', $operator->id)->get();
        foreach ($pools as $pool) {
            $netmask =  $pool->prefix;
            if ($ipv6lib->isInNetmask($lowest_address, $netmask) || $ipv6lib->isInNetmask($highest_address, $netmask)) {
                return $netmask;
            }
        }
        return 0;
    }
}
