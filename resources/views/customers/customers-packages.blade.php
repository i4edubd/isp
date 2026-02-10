@extends('layouts.metronic_demo1')

@section('title')
    Buy Package
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
            $active_link = '1';
        @endphp
        @include('customers.nav-links')
    </div>
    {{-- Navigation bar --}}

    <div class="card-body py-4">
        <div class="mb-6 flex justify-end">
            <form method="GET" action="{{ route('customers.packages') }}" class="flex gap-2">
                <select name="sort" class="px-3 py-1.5 border rounded text-sm">
                    <option value=''>Sort by...</option>
                    <option value='price'>Price</option>
                    <option value='validity'>Validity</option>
                </select>
                <button type="submit" class="px-4 py-1.5 bg-emerald-600 text-white rounded text-sm hover:bg-emerald-700">FILTER</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($packages as $package)
                <div class="card card-flush relative">
                    <div class="absolute top-2 right-2 inline-flex px-3 py-1 bg-rose-600 text-white rounded text-xs font-semibold">
                        {{ $package->price }} {{ config('consumer.currency') }}
                    </div>

                    <div class="card-body pt-8">
                        <h5 class="text-sm font-semibold mb-3">{{ $package->name }}</h5>

                        <ul class="space-y-2 text-sm">
                            <li class="flex justify-between">
                                <span class="text-slate-600">Speed Limit</span>
                                <span class="font-medium">
                                    @if ($package->master_package->rate_limit)
                                        {{ $package->master_package->rate_limit }} {{ $package->master_package->readable_rate_unit }}
                                    @else
                                        Unlimited
                                    @endif
                                </span>
                            </li>

                            <li class="flex justify-between">
                                <span class="text-slate-600">Volume Limit</span>
                                <span class="font-medium">
                                    @if ($package->master_package->volume_limit)
                                        {{ $package->master_package->volume_limit }} {{ $package->volume_unit }}
                                    @else
                                        Unlimited
                                    @endif
                                </span>
                            </li>

                            <li class="flex justify-between">
                                <span class="text-slate-600">Validity</span>
                                <span class="font-medium">{{ $package->master_package->validity }} Days</span>
                            </li>

                            @if ($package->fair_usage_policy)
                                <li class="border-t pt-2 text-xs text-slate-600">
                                    <span class="font-semibold">Fair Usage:</span> After {{ $package->fair_usage_policy->data_limit }} GB, speed drops to {{ $package->fair_usage_policy->speed_limit }} Mbps
                                </li>
                            @endif
                        </ul>

                        <div class="mt-4 pt-4 border-t">
                            <form method="get" action="{{ route('customers.purchase-package', ['package' => $package]) }}" onsubmit="return disableDuplicateSubmit()" class="space-y-2">
                                <label for="payment_gateway_id" class="sr-only">Payment Gateway</label>

                                <select id="payment_gateway_id" name="payment_gateway_id" required class="w-full px-3 py-1.5 border rounded text-sm">
                                    <option value="">Pay With...</option>
                                    @if ($payment_gateways)
                                        @foreach ($payment_gateways as $payment_gateway)
                                            <option value="{{ $payment_gateway->id }}">{{ $payment_gateway->payment_method }}</option>
                                        @endforeach
                                    @endif
                                </select>

                                <button type="submit" id="submit-button" class="w-full px-4 py-1.5 bg-rose-600 text-white rounded text-sm hover:bg-rose-700">{{ getLocaleString($operator->id, 'BUY') }}</button>
                            </form>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        @if($packages->isEmpty())
            <div class="text-center py-8 text-slate-500">
                <p>No packages available.</p>
            </div>
        @endif
    </div>

    @include('customers.footer-nav-links')

</div>
@endsection
