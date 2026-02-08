@extends ('laraview.layouts.sideNavLayout')

@section('title')
Custom Fields
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '4';
$active_link = '6';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@include('admins.components.custom-fields')
