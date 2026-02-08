@extends ('laraview.layouts.sideNavLayout')

@section('title')
Routers
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '12';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection

@section('contentTitle')
<h3>New Router</h3>
@endsection

@include('admins.components.routers-create')
