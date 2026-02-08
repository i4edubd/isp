@extends ('laraview.layouts.sideNavLayout')

@section('title')
Delete Billing Profile
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

    <!--Free IPv4pool-->
    <li class="nav-item">
        <a class="btn btn-outline-success my-2 my-sm-0"
            href="{{ route('billing_profile_replace.edit', ['billing_profile' => $billing_profile->id]) }}">
            <i class="fas fa-minus"></i>
            Replace Billing Profile
        </a>
    </li>
    <!--/Free IPv4pool-->

</ul>

@endsection

@section('content')

<div class="card">

    <div class="card-header text-danger">
        Billing Profile Deletion Failed!
    </div>

    <div class="card-body">

        <h5 class="card-title mb-2"> {{ $customer_count }} Customers are using this billing profile</h5>

    </div>

</div>

@endsection

@section('pageJs')
@endsection
