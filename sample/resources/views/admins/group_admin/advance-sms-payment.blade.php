@extends ('laraview.layouts.sideNavLayout')

@section('title')
SMS Payment
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '10';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@include('admins.components.advance-sms-payment')
