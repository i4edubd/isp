@extends ('laraview.layouts.sideNavLayout')

@section('title')
Account Daily Report
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '3';
$active_link = '5';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@include('admins.components.accounts-daily-report')