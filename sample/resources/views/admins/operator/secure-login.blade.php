@extends ('laraview.layouts.sideNavLayout')

@section ('title')
Secure Login
@endsection

@section ('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '8';
$active_link = '4';
@endphp
@endsection

@section ('sidebar')
@include('admins.operator.sidebar')
@endsection

@include('admins.components.secure-login')
