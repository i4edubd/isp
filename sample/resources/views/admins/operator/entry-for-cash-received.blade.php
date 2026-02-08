@extends ('laraview.layouts.sideNavLayout')

@section('title')
Entry for cash received
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '3';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@include('admins.components.entry-for-cash-received')