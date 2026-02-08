@extends ('laraview.layouts.sideNavLayout')

@section ('title')
Device Identification
@endsection

@section ('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '4';
$active_link = '3';
@endphp
@endsection

@section ('sidebar')
@include('admins.developer.sidebar')
@endsection

@include('admins.components.device-identification')