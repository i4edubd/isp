@extends ('laraview.layouts.sideNavLayout')

@section('title')
Due Date Reminders Helper
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '5';
$active_link = '4';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@include('admins.components.due_date_reminders-helper')
