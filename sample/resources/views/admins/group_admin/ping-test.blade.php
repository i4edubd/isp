@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Ping Test
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '2';
        $active_link = '1';
    @endphp
@endsection

@section('sidebar')
    @include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
    <h3>Ping Test</h3>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb text-danger float-sm-right">
        <li class="breadcrumb-item">Routers & Packages</li>
        <li class="breadcrumb-item active">Ping Test</li>
    </ol>
@endsection

@include('admins.components.ping-test')
