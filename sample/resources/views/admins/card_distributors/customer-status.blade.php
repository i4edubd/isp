@extends ('laraview.layouts.sideNavLayoutForCardDistributors')

@section('title')
    Recharge Status
@endsection

@section('activeLink')
    @php
        $active_menu = '1';
        $active_link = '0';
    @endphp
@endsection

@section('sidebar')
    @include('admins.card_distributors.sidebar')
@endsection

@section('content')
    <div class="card">

        <div class="card-body text-center">

            <div class="card-header bg-info">
                Status
            </div>

            <p> <span class="card-text font-italic font-weight-bold">Customer Name: </span> {{ $customer->name }}</p>
            <p> <span class="card-text font-italic font-weight-bold">Customer Mobile: </span> {{ $customer->mobile }}</p>
            <p> <span class="card-text font-italic font-weight-bold">Customer Status: </span> {{ $customer->status }}</p>
            <p> <span class="card-text font-italic font-weight-bold">Active Package: </span> {{ $customer->package_name }}
            </p>
            <p> <span class="card-text font-italic font-weight-bold">Valid Until: </span> {{ $customer->package_expired_at }}
            </p>

            <a class="btn btn-outline-dark m-2" href="{{ route('card.search-customer.create') }}" role="button">Recharge
                Another</a>
        </div>

    </div>
@endsection

@section('pageJs')
@endsection
