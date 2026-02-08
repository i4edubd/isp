@extends ('laraview.layouts.sideNavLayout')

@section('title')
Operators Payments
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '3';
$active_link = '6';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@include('admins.components.operator-suboperator-payments')

