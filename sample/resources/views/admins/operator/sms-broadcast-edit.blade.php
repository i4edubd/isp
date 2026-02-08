@extends ('laraview.layouts.sideNavLayout')

@section('title')
SMS broadcast
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '7';
$active_link = '4';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@include('admins.components.sms-broadcast-edit')
