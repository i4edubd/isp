@extends ('laraview.layouts.sideNavLayout')

@section('title')
Cash Out
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '13';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection

@include('admins.components.accounts-receivable-cash-out')