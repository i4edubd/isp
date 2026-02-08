@extends ('laraview.layouts.sideNavLayout')

@section('title')
Delete PPPoE Profile
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '4';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection


@section('contentTitle')

<ul class="nav flex-column flex-sm-row">

    <!--Replace PPPoE Profile-->
    <li class="nav-item">
        <a class="btn btn-outline-success my-2 my-sm-0"
            href="{{ route('pppoe_profile_replace.edit', ['pppoe_profile' => $pppoe_profile->id]) }}">
            <i class="fas fa-minus"></i>
            Replace PPPoE Profile
        </a>
    </li>
    <!--/Replace PPPoE Profile -->

</ul>

@endsection

@section('content')

<div class="card">

    <div class="card-header text-danger">
        PPPoE Profile Deletion Failed!
    </div>

    <div class="card-body">

        <h5 class="card-title mb-2">The Following Packages are using this PPPoE Profile :</h5>

        @include('admins.group_admin.pppoe-profiles-packages')

    </div>

</div>

@endsection

@section('pageJs')
@endsection
