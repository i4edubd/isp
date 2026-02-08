@extends ('laraview.layouts.sideNavLayout')

@section ('title')
Activity Logs
@endsection

@section ('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = 'activity_logs';
$active_link = 'activity_logs';
@endphp
@endsection

@section ('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@include('admins.activity_logs.index')
