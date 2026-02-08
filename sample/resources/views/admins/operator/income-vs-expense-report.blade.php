@extends('laraview.layouts.sideNavLayout')

@section ('title')
Income Vs. Expense Report
@endsection

@section ('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '6';
$active_link = '5';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@include('admins.components.income-vs-expense-report')
