@extends ('laraview.layouts.sideNavLayout')

@section ('title')
Device Identification
@endsection

@section ('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '4';
$active_link = '4';
@endphp
@endsection

@section ('sidebar')
@include('admins.manager.sidebar')
@endsection

@include('admins.components.device-identification')