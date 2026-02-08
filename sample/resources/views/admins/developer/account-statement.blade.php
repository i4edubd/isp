@extends ('laraview.layouts.sideNavLayout')

@section('title')
account statement
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '13';
$active_link = $activated_link;
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection

@include('admins.components.account-statement')
