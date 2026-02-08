@extends ('laraview.layouts.sideNavLayout')

@section('title')
Edit Profile
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
@include('admins.operator.sidebar')
@endsection

@include('admins.components.operator-profile-edit')
