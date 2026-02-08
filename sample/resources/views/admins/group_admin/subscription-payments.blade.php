@extends ('laraview.layouts.sideNavLayout')

@section('title')
Subscription Payments
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '9';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@include('admins.components.subscription-payments')
