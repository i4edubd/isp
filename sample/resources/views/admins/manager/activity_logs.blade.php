@extends ('laraview.layouts.sideNavLayout')

@section('title')
Activity Logs
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '4';
$active_link = '3';
@endphp
@endsection

@section('sidebar')
@include('admins.manager.sidebar')
@endsection

@include('admins.components.activity_logs')
