@extends ('laraview.layouts.sideNavLayout')

@section('title')
Distributor payments
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '3';
$active_link = '8';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@include('admins.components.distributor-payments')
