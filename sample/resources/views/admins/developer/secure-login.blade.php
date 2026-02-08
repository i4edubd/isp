@extends ('laraview.layouts.sideNavLayout')

@section ('title')
Secure Login
@endsection

@section ('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '4';
$active_link = '3';
@endphp
@endsection

@section ('sidebar')
@include('admins.developer.sidebar')
@endsection

@include('admins.components.secure-login')