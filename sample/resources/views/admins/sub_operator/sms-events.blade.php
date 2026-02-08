@extends ('laraview.layouts.sideNavLayout')

@section('title')
Event Text Messaging
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '7';
$active_link = '5';
@endphp
@endsection

@section('sidebar')
@include('admins.sub_operator.sidebar')
@endsection

@include('admins.components.sms-events')
