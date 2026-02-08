@extends ('laraview.layouts.sideNavLayout')

@section ('title')
Device Monitors
@endsection

@section ('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '10';
@endphp
@endsection

@section ('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@include('admins.device_monitors.index')
