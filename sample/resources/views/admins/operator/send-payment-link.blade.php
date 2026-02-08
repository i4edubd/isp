@extends ('laraview.layouts.sideNavLayout')

@section('title')
Send Payment Link
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '4';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@include('admins.components.send-payment-link')
