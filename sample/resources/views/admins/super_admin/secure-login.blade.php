@extends ('laraview.layouts.sideNavLayout')

@section ('title')
Secure Login
@endsection

@section ('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '7';
$active_link = '3';
@endphp
@endsection

@section ('sidebar')
@include('admins.super_admin.sidebar')
@endsection

@include('admins.components.secure-login')