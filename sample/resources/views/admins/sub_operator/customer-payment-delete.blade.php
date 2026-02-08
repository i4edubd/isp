@extends ('laraview.layouts.sideNavLayout')

@section('title')
customer's payment delete
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '5';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.sub_operator.sidebar')
@endsection

@include('admins.components.customer-payment-delete')
