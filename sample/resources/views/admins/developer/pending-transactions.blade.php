@extends ('laraview.layouts.sideNavLayout')

@section('title')
Pending Transactions
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '13';
$active_link = '3';
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection

@include('admins.components.pending-transactions')
