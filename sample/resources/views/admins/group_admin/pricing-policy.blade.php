@extends ('laraview.layouts.sideNavLayout')

@section('title')
Pricing Policy
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '14';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection
@section('contentTitle')
@endsection

@section('content')

<h3>By using the software, you are agreeing to be bound by the terms of this pricing policy.</h3>

<div class="card">

    <div class="card-body">

        <dl>

            {{-- Subscription Fee --}}
            <dt>
                Subscription Fee
            </dt>
            <dd>
                1500 {{ config('consumer.currency') }}/Month OR 1 {{ config('consumer.currency') }}/User/Month which is
                higher.
            </dd>
            {{-- Subscription Fee --}}

        </dl>

    </div>

</div>

@endsection

@section('pageJs')
@endsection