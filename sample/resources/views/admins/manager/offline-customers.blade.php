@extends ('laraview.layouts.sideNavLayout')

@section('title')
Offline customers
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '6';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.manager.sidebar')
@endsection

@include('admins.components.offline-customers')
