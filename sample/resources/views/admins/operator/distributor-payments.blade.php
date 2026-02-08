@extends ('laraview.layouts.sideNavLayout')

@section('title')
Distributor payments
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
@include('admins.operator.sidebar')
@endsection

@include('admins.components.distributor-payments')
