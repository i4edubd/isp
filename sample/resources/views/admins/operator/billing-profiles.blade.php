@extends ('laraview.layouts.sideNavLayout')

@section('title')
Billing Profiles
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '5';
$active_link = '7';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection


@section('contentTitle')
<h3> Billing Profiles </h3>
@endsection

@include('admins.components.billing-profiles')
