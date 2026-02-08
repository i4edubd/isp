@extends ('laraview.layouts.sideNavLayout')

@section('title')
SMS Status
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.sales_manager.sidebar')
@endsection

@include('admins.components.sms-status')
