@extends ('laraview.layouts.sideNavLayout')

@section('title')
Download Customer Info
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '4';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.sub_operator.sidebar')
@endsection

@include('admins.components.download-users-downloadable')
