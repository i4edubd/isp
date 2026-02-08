<?php
@extends ('laraview.layouts.sideNavLayout')

@section('title')
Import PPPoE Customers Result
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '5';
$active_link = '4';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
<h3> Import PPPoE Customers Result </h3>
@endsection

@section('content')

<div class="card">
    <div class="card-body">
        <h5>Import Result</h5>
        <ul>
            @foreach ($results as $result)
            <li>{{ $result }}</li>
            @endforeach
        </ul>
    </div>
</div>

@endsection