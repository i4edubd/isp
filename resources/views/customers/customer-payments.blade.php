@extends('layouts.metronic_demo1')

@section('title')
    Payment History
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
            $active_link = '5';
        @endphp
        @include('customers.nav-links')
    </div>
    {{-- Navigation bar --}}

    <div class="card-body py-4">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-slate-100">
                    <tr class="text-slate-700 font-medium">
                        <th class="px-4 py-3">Payment Date</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Transaction ID</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($payments as $payment)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">{{ $payment->date }}</td>
                            <td class="px-4 py-3 font-medium">{{ $payment->amount_paid }}</td>
                            <td class="px-4 py-3">
                                @if($payment->pay_status == 'success')
                                    <span class="inline-flex px-2 py-1 rounded text-xs bg-emerald-100 text-emerald-800">{{ $payment->pay_status }}</span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">{{ $payment->pay_status }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $payment->bank_txnid ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($payments->isEmpty())
            <div class="text-center py-8 text-slate-500">
                <p>No payment records found.</p>
            </div>
        @endif
    </div>

    @include('customers.footer-nav-links')

</div>
@endsection
