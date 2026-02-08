@extends ('laraview.layouts.sideNavLayout')

@section('title')
Online Customers
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '4';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@include('admins.components.online-customers')
