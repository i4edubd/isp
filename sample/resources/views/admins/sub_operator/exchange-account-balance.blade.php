@extends ('laraview.layouts.sideNavLayout')

@section('title')
Exchange Balance
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

@include('admins.components.exchange-account-balance')