@extends ('laraview.layouts.sideNavLayout')

@section('title')
Online Recharge
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '3';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.sub_operator.sidebar')
@endsection

@include('admins.components.accounts-onlinePayment')