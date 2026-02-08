@extends ('laraview.layouts.sideNavLayout')

@section('title')
Entry for cash received
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '13';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection

@include('admins.components.entry-for-cash-received')