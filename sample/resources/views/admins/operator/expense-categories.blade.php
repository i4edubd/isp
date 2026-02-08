@extends ('laraview.layouts.sideNavLayout')

@section('title')
Expense Categories
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
@include('admins.operator.sidebar')
@endsection

@include('admins.components.expense-categories')
