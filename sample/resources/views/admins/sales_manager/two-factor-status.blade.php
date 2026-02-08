@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Two Factor
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '4';
        $active_link = '2';
    @endphp
@endsection

@section('sidebar')
    @include('admins.sales_manager.sidebar')
@endsection

@include('admins.components.two-factor-status')
