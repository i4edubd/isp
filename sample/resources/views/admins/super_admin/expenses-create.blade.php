@extends ('laraview.layouts.sideNavLayout')

@section('title')
New Expense
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '6';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.super_admin.sidebar')
@endsection

@include('admins.components.expenses-create')
