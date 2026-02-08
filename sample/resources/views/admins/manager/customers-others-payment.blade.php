@extends ('laraview.layouts.sideNavLayout')

@section('title')
customers other Payment
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '3';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.manager.sidebar')
@endsection

@include('admins.components.customers-others-payment')
