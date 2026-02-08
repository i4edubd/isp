@extends ('laraview.layouts.sideNavLayout')

@section('title')
Accounts Payable
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '13';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection

@include('admins.components.accounts-payable')
