@extends ('laraview.layouts.sideNavLayout')

@section('title')
Packages
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.sub_operator.sidebar')
@endsection


@section('contentTitle')
<h3> Packages </h3>
@endsection

@include('admins.components.operator-packages')
