@extends ('laraview.layouts.sideNavLayout')

@section('title')
Activity logs
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '8';
$active_link = '3';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@include('admins.components.activity_logs')
