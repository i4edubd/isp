@extends ('laraview.layouts.sideNavLayout')

@section('title')
Incomes Summary
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '7';
$active_link = '4';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@include('admins.components.operator_incomes-summary')
