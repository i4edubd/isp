@extends ('laraview.layouts.sideNavLayout')

@section('title')
customer's bill Edit
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '5';
$active_link = '3';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@include('admins.components.customers-bill-edit')
