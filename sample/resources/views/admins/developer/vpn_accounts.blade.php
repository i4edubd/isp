@extends ('laraview.layouts.sideNavLayout')

@section('title')
vpn accounts
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '12';
$active_link = '3';
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection

@include('admins.components.vpn_accounts')
