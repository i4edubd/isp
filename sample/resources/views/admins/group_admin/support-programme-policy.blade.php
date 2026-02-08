@extends ('laraview.layouts.sideNavLayout')

@section('title')
Affiliate Program Policy
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '30';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">Affiliate Program</li>
    <li class="breadcrumb-item active">Policy</li>
</ol>
@endsection

@section('content')

<h3>
    By enrolling in the affiliate program, you are agreeing to be bound by the terms of this affiliate program policy.
</h3>

<div class="card">

    <div class="card-body">

        <dl>

            {{-- Commission --}}
            <dt>
                Commission
            </dt>
            <dd>
                <ol>
                    <li>
                        Affiliate/Publisher will get monthly {{ config('consumer.affiliate_commission_rate') }}%
                        commission for
                        each successful subscription payment.
                    </li>
                    <li>
                        @php
                        $payout = currencyConversion(1000.00, getCurrency(Auth::user()->id));
                        @endphp
                        Minimum Payout Amount: {{ $payout->get('amount') }} {{ $payout->get('currency_code') }}
                    </li>
                </ol>
            </dd>
            {{-- Commission --}}

            {{-- Leads --}}
            <dt>
                Leads
            </dt>
            <dd>
                <ol>
                    <li>
                        Leads are the software subscribers created through Affiliate/Publisher.
                    </li>
                </ol>
            </dd>
            {{-- Leads --}}

            {{-- key account manager --}}
            <dt>
                Key Account Manager
            </dt>
            <dd>
                <ol>
                    <li>Affiliate/Publisher will be the Key Account Manager of his/her Leads.</li>
                    <li>Affiliate/Publisher will provide the first level support regarding the software to his/her
                        Leads.</li>
                </ol>
            </dd>
            {{-- key account manager --}}

            {{-- Confidential --}}
            <dt>Confidential</dt>
            <dd>None of the Parties will disclose the confidential information of Clients.</dd>
            {{-- Confidential --}}

        </dl>

    </div>

</div>

@endsection

@section('pageJs')
@endsection