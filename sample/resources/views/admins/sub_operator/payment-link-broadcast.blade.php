@extends ('laraview.layouts.sideNavLayout')

@section('title')
payment link broadcast
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '5';
$active_link = '5';
@endphp
@endsection

@section('sidebar')
@include('admins.sub_operator.sidebar')
@endsection

@include('admins.components.payment-link-broadcast')
