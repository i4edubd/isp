@extends ('laraview.layouts.sideNavLayout')

@section('title')
customers bills
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '5';
$active_link = '9';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@include('admins.components.customers-bills-summary')
