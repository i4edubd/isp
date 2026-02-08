@extends ('laraview.layouts.sideNavLayout')

@section('title')
SMS Bill Edit
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '7';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.sub_operator.sidebar')
@endsection

@include('admins.components.sms-bills-edit')
