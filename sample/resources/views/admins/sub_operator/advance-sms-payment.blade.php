@extends ('laraview.layouts.sideNavLayout')

@section('title')
Advance SMS Payment
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '7';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.sub_operator.sidebar')
@endsection

@include('admins.components.advance-sms-payment')
