@extends ('laraview.layouts.sideNavLayout')

@section('title')
Expenses Edit
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
@include('admins.manager.sidebar')
@endsection

@include('admins.components.expenses-edit')
