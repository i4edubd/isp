@extends ('laraview.layouts.sideNavLayout')

@section('title')
Change Password
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '4';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.sales_manager.sidebar')
@endsection


@section('contentTitle')

<h3>Change Password</h3>

@endsection

@include('admins.components.change-password')
