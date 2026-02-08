@extends ('laraview.layouts.sideNavLayout')

@section('title')
Operator Packages
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '10';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@section('contentTitle')

<form action="{{ route('operators.packages.create', ['operator' => $operator->id]) }}">

    <ul class="nav flex-column flex-sm-row">
        <li class="nav-item">
            <button type="submit" class="btn btn-primary ml-2"> <i class="fas fa-plus"></i> Add Package</button>
        </li>
    </ul>

</form>

@endsection

@include('admins.components.operator-packages')
