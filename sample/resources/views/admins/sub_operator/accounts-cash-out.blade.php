@extends ('laraview.layouts.sideNavLayout')

@section('title')
Cash Out
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '3';
$active_link = $activated_link;
@endphp
@endsection

@section('sidebar')
@include('admins.sub_operator.sidebar')
@endsection

@include('admins.components.accounts-cash-out')