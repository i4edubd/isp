@extends ('laraview.layouts.sideNavLayout')

@section ('title')
Edit Device Monitor
@endsection

@section ('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '14';
$active_link = '0';
@endphp
@endsection

@section ('sidebar')
@include('admins.developer.sidebar')
@endsection

@include('admins.device_monitors.edit')
