@extends ('laraview.layouts.sideNavLayout')

@section('title')
Comments
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '1';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.sales_manager.sidebar')
@endsection

@include('admins.components.sales-comments')
