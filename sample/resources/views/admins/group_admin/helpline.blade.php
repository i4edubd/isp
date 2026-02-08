@extends ('laraview.layouts.sideNavLayout')

@section('title')
Helpline
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '12';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection


@section('contentTitle')

<h3> Helpline </h3>

@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <dl class="row">
            <dt class="col-sm-4"><i class="fab fa-whatsapp-square"></i> HelpLine </dt>
            <dd class="col-sm-8">{{ getSoftwareSupportNumber(Auth::user()->id) }}</dd>
        </dl>

        <hr>

        <dl class="row">
            <dt class="col-sm-4"><i class="fas fa-envelope-open-text"></i> Email</dt>
            <dd class="col-sm-8">{{ config('consumer.helpline_email') }}</dd>
        </dl>

    </div>

    <div class="card-footer">
        <h3 class="font-italic display-6">HelpLine Notes:</h3>
        <ol>
            <li>
                {{ getLocaleString(Auth::user()->id, 'one problem at a time.') }}
            </li>
            <li>
                {{ getLocaleString(Auth::user()->id, 'Please tell us in one line if you can.') }}
            </li>
            <li>
                {{ getLocaleString(Auth::user()->id, 'Please express your issue politely for better service.') }}
            </li>
        </ol>
    </div>

</div>

@endsection

@section('pageJs')
@endsection