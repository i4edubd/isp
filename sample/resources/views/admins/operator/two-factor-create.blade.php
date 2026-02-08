@extends ('laraview.layouts.sideNavLayout')

@section ('title')
Two Factor
@endsection

@section ('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '8';
$active_link = '2';
@endphp
@endsection

@section ('sidebar')
@include('admins.operator.sidebar')
@endsection

@include('admins.components.two-factor-create')