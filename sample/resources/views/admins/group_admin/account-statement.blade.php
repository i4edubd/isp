@extends ('laraview.layouts.sideNavLayout')

@section('title')
account statement
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '3';
$active_link = $activated_link;
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@include('admins.components.account-statement')
