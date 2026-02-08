@extends ('laraview.layouts.topNavLayout')

@section('title')
    Send Money
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
                $active_link = '17';
            @endphp
            @include('customers.nav-links')
        </div>
        {{-- Navigation bar --}}

        <div class="card-body">
            <p>Thanks for Send Money. An operator will verify the payment.</p>
        </div>

    </div>
@endsection

@section('pageJs')
@endsection
