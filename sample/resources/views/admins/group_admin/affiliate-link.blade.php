@extends ('laraview.layouts.sideNavLayout')

@section('title')
Affiliate Link
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '30';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
<h3>Affiliate Link</h3>
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">Support Programme</li>
    <li class="breadcrumb-item active">Affiliate Link</li>
</ol>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <p>{{ $affiliate_link }}</p>

    </div>

</div>

@endsection

@section('pageJs')
@endsection