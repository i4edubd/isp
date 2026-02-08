@extends ('laraview.layouts.sideNavLayout')

@section('title')
SMS Payments
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '4';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.super_admin.sidebar')
@endsection

@include('admins.components.sms_balance_histories')
