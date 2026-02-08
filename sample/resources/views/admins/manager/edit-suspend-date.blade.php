@extends ('laraview.layouts.sideNavLayout')

@section('title')
Edit Suspend Date
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '1';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.manager.sidebar')
@endsection

@include('admins.components.edit-suspend-date')
