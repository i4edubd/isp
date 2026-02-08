@extends ('laraview.layouts.sideNavLayout')

@section('title')
Incomes Summary
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '6';
$active_link = '4';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@include('admins.components.operator_incomes-summary')
