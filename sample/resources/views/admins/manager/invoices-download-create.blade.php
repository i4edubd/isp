@extends ('laraview.layouts.sideNavLayout')

@section('title')
Invoices Download
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '3';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.manager.sidebar')
@endsection

@include('admins.components.invoices-download-create')
