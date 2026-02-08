@extends ('laraview.layouts.sideNavLayout')

@section('title')
vpn accounts
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '8';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@include('admins.components.vpn_accounts')
