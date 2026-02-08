@extends ('laraview.layouts.sideNavLayout')

@section('title')
Edit Expense
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
@include('admins.operator.sidebar')
@endsection

@include('admins.components.expenses-edit')
