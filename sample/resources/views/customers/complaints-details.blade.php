@extends ('laraview.layouts.topNavLayout')

@section('title')
    Complaint Details
@endsection

@section('pageCss')
@endsection

@section('company')
    {{ $operator->company }}
@endsection

@section('topNavbar')
@endsection

@section('contentTitle')
    @include('customers.logout-nav')
@endsection

@section('content')
    <div class="card">

        {{-- Navigation bar --}}
        <div class="card-header">
            @php
                $active_link = '6';
            @endphp
            @include('customers.nav-links')
        </div>
        {{-- Navigation bar --}}

        <div class="card-body">

            @include('complaint_management.complaint-timeline')

        </div>

        @include('customers.footer-nav-links')

    </div>
@endsection

@section('pageJs')
@endsection
