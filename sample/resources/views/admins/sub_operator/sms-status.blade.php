@extends ('laraview.layouts.sideNavLayout')

@section('title')
SMS Status
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '7';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.sub_operator.sidebar')
@endsection

@include('admins.components.sms-status')
