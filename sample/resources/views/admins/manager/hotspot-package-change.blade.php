@extends ('laraview.layouts.sideNavLayout')

@section('title')
Package Change
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '1';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.manager.sidebar')
@endsection

@include('admins.components.hotspot-package-change')