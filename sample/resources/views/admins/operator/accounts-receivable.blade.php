@extends ('laraview.layouts.sideNavLayout')

@section('title')
Accounts Receivable
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '3';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@include('admins.components.accounts-receivable')
