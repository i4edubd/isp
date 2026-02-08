@extends ('laraview.layouts.sideNavLayout')

@section('title')
video tutorials
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '12';
$active_link = '3';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection


@section('contentTitle')

<h3> Video Tutorials </h3>

@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <ul class="list-group list-group-flush">

            <li class="list-group-item">
                <i class="fas fa-video"></i>
                <a class="btn btn-outline-success"
                    href="#" role="button"
                    target="_blank">
                    Video Tutorials
                </a>
            </li>

        </ul>

    </div>

</div>

@endsection

@section('pageJs')
@endsection
