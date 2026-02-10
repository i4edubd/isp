@extends('layouts.metronic_demo1')

@section('title')
    Profile
@endsection

@section('company')
    {{ $operator->company }}
@endsection

@section('contentTitle')
    @include('customers.logout-nav')
@endsection

@section('content')

<div class="card card-flush">

    {{-- Navigation bar --}}
    <div class="card-header">
        @php
            $active_link = '0';
        @endphp
        @include('customers.nav-links')
    </div>
    {{-- Navigation bar --}}

    <div class="card-body">
        @if ($customer->status == 'active')
            <h5 class="card-title text-success">{{ getLocaleString($operator->id, 'Status') }} : {{ $customer->status }}</h5>
        @else
            <h5 class="card-title text-rose-600">{{ getLocaleString($operator->id, 'Status') }} : {{ $customer->status }}</h5>
        @endif
    </div>

    <div class="divide-y">
        <div class="p-4">
            <div class="text-sm text-slate-600">{{ getLocaleString($operator->id, 'Connection Type') }}</div>
            <div class="font-medium">{{ $customer->connection_type }}</div>
        </div>
        <div class="p-4">
            <div class="text-sm text-slate-600">{{ getLocaleString($operator->id, 'Name') }}</div>
            <div class="font-medium">{{ $customer->name }}</div>
        </div>
        <div class="p-4">
            <div class="text-sm text-slate-600">{{ getLocaleString($operator->id, 'Mobile') }}</div>
            <div class="font-medium">{{ $customer->mobile }}</div>
        </div>
        <div class="p-4">
            <div class="text-sm text-slate-600">{{ getLocaleString($operator->id, 'Username') }}</div>
            <div class="font-medium">{{ $customer->username }}</div>
        </div>
        <div class="p-4">
            <div class="text-sm text-slate-600">{{ getLocaleString($operator->id, 'Password') }}</div>
            <div class="font-medium">{{ $customer->password }}</div>
        </div>
        <div class="p-4">
            <div class="text-sm text-slate-600">{{ getLocaleString($operator->id, 'Active Package') }}</div>
            <div class="font-medium">{{ $customer->package_name }}</div>
        </div>
        <div class="p-4">
            <div class="text-sm text-slate-600">{{ getLocaleString($operator->id, 'Package Updated At') }}</div>
            <div class="font-medium">{{ $customer->last_recharge_time }}</div>
        </div>
        <div class="p-4">
            <div class="text-sm text-slate-600">{{ getLocaleString($operator->id, 'Valid Untill') }}</div>
            <div class="font-medium">{{ $customer->package_expired_at }}</div>
        </div>

        @if ($customer->connection_type == 'Hotspot')
            <div class="p-4">
                @if ($customer->rate_limit)
                    <div class="text-sm text-slate-600">{{ getLocaleString($operator->id, 'Speed Limit') }}</div>
                    <div class="font-medium">{{ $customer->rate_limit }} Mbps</div>
                @else
                    <div class="text-sm text-slate-600">{{ getLocaleString($operator->id, 'Speed Limit') }}</div>
                    <div class="font-medium">No Limit</div>
                @endif
            </div>

            <div class="p-4">
                @if ($customer->total_octet_limit)
                    <div class="text-sm text-slate-600">{{ getLocaleString($operator->id, 'Volume Limit') }}</div>
                    <div class="font-medium">{{ $customer->total_octet_limit / 1000000 }} MB</div>
                @else
                    <div class="text-sm text-slate-600">{{ getLocaleString($operator->id, 'Volume Limit') }}</div>
                    <div class="font-medium">Unlimited MB</div>
                @endif
            </div>

            <div class="p-4">
                <div class="text-sm text-slate-600">{{ getLocaleString($operator->id, 'Volume Used') }}</div>
                <div class="font-medium">{{ ($customer->radaccts->sum('acctoutputoctets') +
                        $customer->radaccts->sum('acctinputoctets') +
                        $radaccts_history->sum('acctoutputoctets') +
                        $radaccts_history->sum('acctinputoctets')) /
                        1000 /
                        1000 /
                        1000 }}
                    GB
                </div>
            </div>
        @endif

    </div>

    @include('customers.footer-nav-links')

</div>

@endsection
