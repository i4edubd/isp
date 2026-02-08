@extends ('laraview.layouts.sideNavLayout')

@section ('title')
Dashboard
@endsection

@section ('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '0';
$active_link = '0';
@endphp
@endsection

@section ('sidebar')
@include('admins.sales_manager.sidebar')
@endsection

@include('admins.components.dashboard')
