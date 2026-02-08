@extends ('laraview.layouts.sideNavLayout')

@section('title')
New Expense Category
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '6';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.super_admin.sidebar')
@endsection

@include('admins.components.expense-category-create')
