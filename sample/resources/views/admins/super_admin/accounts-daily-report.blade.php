@extends ('laraview.layouts.sideNavLayout')

@section('title')
Account daily Report
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '5';
@endphp
@endsection

@section('sidebar')
@include('admins.super_admin.sidebar')
@endsection

@include('admins.components.accounts-daily-report')