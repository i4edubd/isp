@extends('laraview.layouts.sideNavLayout')

@section ('title')
Expense Report
@endsection

@section ('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '7';
$active_link = '3';
@endphp
@endsection


@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@include('admins.components.expense-report')
