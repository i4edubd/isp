@extends ('laraview.layouts.sideNavLayout')

@section('title')
ISP Information
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '5';
$active_link = '6';
@endphp
@endsection

@section('sidebar')
@include('admins.sub_operator.sidebar')
@endsection

@include('admins.components.isp-informations-create')
