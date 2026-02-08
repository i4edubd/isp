@extends ('laraview.layouts.sideNavLayout')

@section('title')
Account Edit
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '3';
$active_link = $activated_link;
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@include('admins.components.account-edit')