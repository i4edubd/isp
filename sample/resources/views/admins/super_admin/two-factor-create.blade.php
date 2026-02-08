@extends ('laraview.layouts.sideNavLayout')

@section ('title')
Two Factor
@endsection

@section ('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '7';
$active_link = '2';
@endphp
@endsection

@section ('sidebar')
@include('admins.super_admin.sidebar')
@endsection

@include('admins.components.two-factor-create')
