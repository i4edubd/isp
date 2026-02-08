@extends ('laraview.layouts.sideNavLayout')

@section('title')
customers
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '1';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.manager.sidebar')
@endsection

@section('contentTitle')
@endsection

@section('content')
<div class="card">
    @include('admins.components.customer-details')
</div>
@endsection

@section('pageJs')
@endsection
