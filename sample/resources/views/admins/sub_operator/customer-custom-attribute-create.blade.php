@extends ('laraview.layouts.sideNavLayout')

@section('title')
Customer Custom Attribute
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '4';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.sub_operator.sidebar')
@endsection

@include('admins.components.customer-custom-attribute-create')
