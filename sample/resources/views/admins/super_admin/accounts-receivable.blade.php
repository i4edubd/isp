@extends ('laraview.layouts.sideNavLayout')

@section('title')
Accounts Receivable
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

@include('admins.components.accounts-receivable')
