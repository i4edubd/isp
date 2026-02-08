@extends('laraview.layouts.sideNavLayout')

@section ('title')
Income Vs. Expense Report
@endsection

@section ('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '6';
$active_link = '4';
@endphp
@endsection


@section('sidebar')
@include('admins.super_admin.sidebar')
@endsection

@include('admins.components.income-vs-expense-report')
