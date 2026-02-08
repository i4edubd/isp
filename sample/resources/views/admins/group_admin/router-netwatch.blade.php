@extends ('laraview.layouts.sideNavLayout')

@section('title')
    netwatch
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

@include('admins.components.router-netwatch')
