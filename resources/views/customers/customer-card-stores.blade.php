@extends('layouts.metronic_demo1')

@section('title')
    Card Stores
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
            $active_link = '3';
        @endphp
        @include('customers.nav-links')
    </div>
    {{-- Navigation bar --}}

    <div class="card-body py-4">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-slate-100">
                    <tr class="text-slate-700 font-medium">
                        <th class="px-4 py-3">Store Name</th>
                        <th class="px-4 py-3">Store Address</th>
                        <th class="px-4 py-3">Contact Number</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($card_distributors as $card_distributor)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium">{{ $card_distributor->store_name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $card_distributor->store_address }}</td>
                            <td class="px-4 py-3">
                                <a href="tel:{{ $card_distributor->mobile }}" class="text-blue-600 hover:underline">{{ $card_distributor->mobile }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($card_distributors->isEmpty())
            <div class="text-center py-8 text-slate-500">
                <p>No card stores found.</p>
            </div>
        @endif
    </div>

    @include('customers.footer-nav-links')

</div>
@endsection
