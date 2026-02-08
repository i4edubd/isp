@extends ('laraview.layouts.sideNavLayout')

@section('title')
Activity Logs
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '8';
$active_link = '5';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@include('admins.components.authentication_logs')