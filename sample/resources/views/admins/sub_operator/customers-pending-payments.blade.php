@extends ('laraview.layouts.sideNavLayout')

@section('title')
Customers Pending Payments
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '5';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.sub_operator.sidebar')
@endsection

@include('admins.components.customers-pending-payments')
