@extends ('laraview.layouts.sideNavLayout')

@section('title')
Due Date Reminders
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '6';
$active_link = '4';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@include('admins.components.generate-due-date-reminders-preview')
