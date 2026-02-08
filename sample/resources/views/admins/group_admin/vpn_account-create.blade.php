@extends ('laraview.layouts.sideNavLayout')

@section('title')
create vpn account
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '8';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
<h3>New vpn account</h3>
@endsection

@section('content')

<div class="card card-outline card-primary">

    <div class="card-header">
    </div>

    <div class="card-body">
        For each VPN account 50 {{ config('consumer.currency') }} will be added to subscription bill.
    </div>

    <div class="card-footer">
        <form method="POST" action="{{ route('vpn_accounts.store') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>

</div>

@endsection

@section('pageJs')
@endsection
