@extends('layouts.metronic_demo1')

@section('title')
    Bills
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
            $active_link = '4';
        @endphp
        @include('customers.nav-links')
    </div>
    {{-- Navigation bar --}}

    <div class="card-body py-4">
        @foreach ($bills as $bill)
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
                        <div>
                            <div class="text-sm text-slate-600">Username</div>
                            <div class="font-medium">{{ $bill->username }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-slate-600">Mobile</div>
                            <div class="font-medium">{{ $bill->mobile }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-slate-600">Amount</div>
                            <div class="font-medium">{{ $bill->amount }}</div>
                        </div>
                    </div>

                    <div class="mt-3 text-sm text-slate-600">Description</div>
                    <div class="text-sm">{{ $bill->description }}</div>

                    <div class="mt-3">
                        <div class="text-sm text-slate-600">Billing Period</div>
                        <div class="text-sm">{{ $bill->billing_period }}</div>
                    </div>

                    <div class="mt-3">
                        <div class="text-sm text-slate-600">Due Date</div>
                        <div class="text-sm">{{ $bill->due_date }}</div>
                    </div>

                    <div class="mt-4">
                        <form method="get" action="{{ route('customers.pay-bill', ['customer_bill' => $bill]) }}" onsubmit="return disableDuplicateSubmit()" class="flex items-center gap-3">

                            <label for="payment_gateway_id" class="sr-only">Payment Gateway</label>

                            <select id="payment_gateway_id" name="payment_gateway_id" required class="form-select px-3 py-1 border rounded text-sm">
                                <option value="">Pay With...</option>

                                @if ($payment_gateways)
                                    @foreach ($payment_gateways as $payment_gateway)
                                        <option value="{{ $payment_gateway->id }}">{{ $payment_gateway->payment_method }}</option>
                                    @endforeach
                                @endif

                            </select>

                            <button type="submit" id="submit-button" class="inline-flex items-center px-4 py-1.5 bg-rose-600 text-white rounded text-sm">PAY</button>

                        </form>
                    </div>

                </div>
            </div>
        @endforeach

        @include('customers.footer-nav-links')

    </div>
</div>
@endsection
