@extends ('laraview.layouts.sideNavLayout')

@section('title')
Billing Profiles
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '5';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection


@section('contentTitle')

<ul class="nav flex-column flex-sm-row">

    <!--New Billing Profile-->
    <li class="nav-item">
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('temp_billing_profiles.create') }}">
            <i class="fas fa-plus"></i>
            Create or Update Billing Profile
        </a>
    </li>
    <!--/New Billing Profile-->

    <!--Billing Profile Helper-->
    <li class="nav-item ml-4">
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('billing-profile-helper.create') }}">
            <i class="fas fa-heart"></i>
            Billing Profile Helper
        </a>
    </li>
    <!--/Billing Profile Helper-->

</ul>

@endsection

@include('admins.components.billing-profiles')
