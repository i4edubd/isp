@extends ('laraview.layouts.sideNavLayout')

@section('title')
Verify Payments
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '5';
$active_link = '8';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@include('admins.components.verify-customers-payments')
