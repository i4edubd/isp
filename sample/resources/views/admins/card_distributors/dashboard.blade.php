@extends ('laraview.layouts.sideNavLayoutForCardDistributors')

@section('title')
    Dashboard
@endsection

@section('activeLink')
    @php
        $active_menu = '0';
        $active_link = '0';
    @endphp
@endsection

@section('sidebar')
    @include('admins.card_distributors.sidebar')
@endsection

@section('contentTitle')
    <h3>Dashboard</h3>
@endsection

@section('content')
    <div class="card border border-danger">

        <div class="card-header">
            <h3>Account Balance</h3>
        </div>

        <div class="card-body">

            <div class="row">

                {{-- Account Balance --}}
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="collected_amount">{{ $balance }}</h3>
                            <p>Account Balance</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                    </div>
                </div>
                {{-- Account Balance --}}

            </div>

        </div>

    </div>

    <div class="card border border-info">

        <div class="card-header">
            <h3>Quick Links</h3>
        </div>

        <div class="card-body">

            <a class="btn btn-outline-success btn-lg m-2" href="{{ route('card.search-customer.create') }}"
                role="button">Top-up / Pay Bill</a>
            <a class="btn btn-outline-dark btn-lg m-2" href="{{ route('card.recharge-history') }}" role="button">Recharge
                History</a>
            <a class="btn btn-outline-info btn-lg m-2" href="{{ route('card.payment-history') }}" role="button">Payment
                History</a>
            <a class="btn btn-outline-danger btn-lg m-2" href="{{ route('card.change-password.create') }}"
                role="button">Change Password</a>

        </div>

    </div>
@endsection

@section('pageJs')
@endsection
