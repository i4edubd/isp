@extends('layouts.metronic_demo1')

@section('title')
    Internet History
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
            $active_link = '2';
        @endphp
        @include('customers.nav-links')
    </div>
    {{-- Navigation bar --}}

    <div class="card-body py-4">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-slate-100">
                    <tr class="text-slate-700 font-medium">
                        <th class="px-4 py-3">Start Time</th>
                        <th class="px-4 py-3">Stop Time</th>
                        <th class="px-4 py-3">Total Time</th>
                        <th class="px-4 py-3">Terminate Cause</th>
                        <th class="px-4 py-3">Download (MB)</th>
                        <th class="px-4 py-3">Upload (MB)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @php
                        $total_download = 0;
                        $total_upload = 0;
                    @endphp
                    @foreach ($customer->radaccts->sortBy('acctstoptime') as $radacct)
                        @php
                            $total_download = $total_download + $radacct->acctoutputoctets;
                            $total_upload = $total_upload + $radacct->acctinputoctets;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">{{ $radacct->acctstarttime }}</td>
                            <td class="px-4 py-3">{{ $radacct->acctstoptime }}</td>
                            <td class="px-4 py-3">{{ sToHms($radacct->acctsessiontime) }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $radacct->acctterminatecause }}</td>
                            <td class="px-4 py-3 font-medium">{{ round($radacct->acctoutputoctets / 1000000, 2) }}</td>
                            <td class="px-4 py-3 font-medium">{{ round($radacct->acctinputoctets / 1000000, 2) }}</td>
                        </tr>
                    @endforeach
                    @foreach ($radaccts_history->sortByDesc('acctstoptime') as $radacct_history)
                        @php
                            $total_download = $total_download + $radacct_history->acctoutputoctets;
                            $total_upload = $total_upload + $radacct_history->acctinputoctets;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">{{ $radacct_history->acctstarttime }}</td>
                            <td class="px-4 py-3">{{ $radacct_history->acctstoptime }}</td>
                            <td class="px-4 py-3">{{ sToHms($radacct_history->acctsessiontime) }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $radacct_history->acctterminatecause }}</td>
                            <td class="px-4 py-3 font-medium">{{ round($radacct_history->acctoutputoctets / 1000000, 2) }}</td>
                            <td class="px-4 py-3 font-medium">{{ round($radacct_history->acctinputoctets / 1000000, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-slate-50 font-semibold">
                        <td class="px-4 py-3"></td>
                        <td class="px-4 py-3"></td>
                        <td class="px-4 py-3"></td>
                        <td class="px-4 py-3">Total:</td>
                        <td class="px-4 py-3">{{ round($total_download / 1000000, 2) }}</td>
                        <td class="px-4 py-3">{{ round($total_upload / 1000000, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @include('customers.footer-nav-links')

</div>
@endsection
