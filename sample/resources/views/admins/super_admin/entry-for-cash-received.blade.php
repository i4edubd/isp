@extends ('laraview.layouts.sideNavLayout')

@section('title')
Entry for cash received
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.super_admin.sidebar')
@endsection

@include('admins.components.entry-for-cash-received')