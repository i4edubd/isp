@extends ('laraview.layouts.sideNavLayout')

@section('title')
SMS History
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.sales_manager.sidebar')
@endsection

@include('admins.components.sms-histories')
