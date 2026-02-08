@extends ('laraview.layouts.sideNavLayout')

@section('title')
Group admin
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '1';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.super_admin.sidebar')
@endsection

@section('contentTitle')
<h3> Group Admin </h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <dl class="row">
            <dt class="col-sm-4">Group Admin's Name</dt>
            <dd class="col-sm-8">{{ $group_admin->name }}</dd>
        </dl>

        <hr>

        <dl class="row">
            <dt class="col-sm-4">Mobile</dt>
            <dd class="col-sm-8">{{ $group_admin->mobile }}</dd>
        </dl>

        <hr>

        <dl class="row">
            <dt class="col-sm-4">Email</dt>
            <dd class="col-sm-8">

                @if ($group_admin->email_verified_at)
                <i class="far fa-check-circle"></i>
                @else
                <i class="far fa-times-circle"></i>
                @endif

                {{ $group_admin->email }}

            </dd>
        </dl>

        <hr>

        <dl class="row">
            <dt class="col-sm-4">Company Name</dt>
            <dd class="col-sm-8">{{ $group_admin->company }}</dd>
        </dl>

        <hr>

        <dl class="row">
            <dt class="col-sm-4">Status</dt>
            <dd class="col-sm-8">{{ $group_admin->status }}</dd>
        </dl>

        <hr>

        <dl class="row">
            <dt class="col-sm-4">Subscription Type</dt>
            <dd class="col-sm-8">{{ $group_admin->subscription_type }}</dd>
        </dl>

        <hr>

        <dl class="row">
            <dt class="col-sm-4">Subscription Status</dt>
            <dd class="col-sm-8">{{ $group_admin->subscription_status }}</dd>
        </dl>

        <hr>

        <dl class="row">
            <dt class="col-sm-4">Registration Date</dt>
            <dd class="col-sm-8">{{ $group_admin->created_at }} ({{ $membership_time }})</dd>
        </dl>

    </div>

</div>

@endsection

@section('pageJs')
@endsection