@extends ('laraview.layouts.sideNavLayout')

@section('title')
Make Child
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '4';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@section('contentTitle')
<h3>Make Child</h3>
@endsection

@include('admins.components.make-child')