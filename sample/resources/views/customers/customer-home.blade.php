@extends ('laraview.layouts.topNavLayout')

@section('title')
    Home
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
    <div class="container-fluid">

        <div class="row">

            <div class="col">
                <div class="card text-center">
                    <div class="card-body">
                        {{-- Card Recharge --}}
                        <a class="btn bg-maroon" href="{{ route('customers.card-recharge.create') }}">
                            <i class="far fa-credit-card"></i>
                            <h6> {{ getLocaleString($operator->id, 'Card Recharge') }} </h6>
                        </a>
                        {{-- Card Recharge --}}
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card text-center">
                    <div class="card-body">
                        {{-- Buy Package --}}
                        <a class="btn bg-success" href="{{ route('customers.packages') }}">
                            <i class="fas fa-store"></i>
                            <h6> {{ getLocaleString($operator->id, 'Buy Package') }} </h6>
                        </a>
                        {{-- Buy Package --}}
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card text-center">
                    <div class="card-body">
                        {{-- Card Stores --}}
                        <a class="btn bg-orange" href="{{ route('customers.card-stores') }}">
                            <i class="fas fa-store"></i>
                            <h6> {{ getLocaleString($operator->id, 'Card Stores') }} </h6>
                        </a>
                        {{-- Card Stores --}}
                    </div>
                </div>
            </div>

        </div>

    </div>


    <div class="container-fluid">

        <div class="row">

            <div class="col">
                <div class="card text-center">
                    <div class="card-body">
                        {{-- Profile --}}
                        <a class="btn bg-info" href="{{ route('customers.profile') }}">
                            <i class="fas fa-user"></i>
                            <h6> {{ getLocaleString($operator->id, 'Profile') }} </h6>
                        </a>
                        {{-- Profile --}}
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card text-center">
                    <div class="card-body">
                        {{-- Internet History --}}
                        <a class="btn bg-navy" href="{{ route('customers.radaccts') }}">
                            <i class="fas fa-history"></i>
                            <h6> {{ getLocaleString($operator->id, 'Internet History') }} </h6>
                        </a>
                        {{-- Internet History --}}
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card text-center">
                    <div class="card-body">
                        {{-- Bandwidth Graph --}}
                        <a class="btn bg-lightblue" href="{{ route('customers.graph') }}">
                            <i class="fas fa-chart-bar"></i>
                            <h6> {{ getLocaleString($operator->id, 'Bandwidth Graph') }} </h6>
                        </a>
                        {{-- Bandwidth Graph --}}
                    </div>
                </div>
            </div>

        </div>

    </div>


    <div class="container-fluid">

        <div class="row">

            <div class="col">
                <div class="card text-center">
                    <div class="card-body">
                        {{-- Bills --}}
                        <a class="btn bg-purple" href="{{ route('customers.bills') }}">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <h6> {{ getLocaleString($operator->id, 'Bills') }} </h6>
                        </a>
                        {{-- Bills --}}
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card text-center">
                    <div class="card-body">
                        {{-- Payment History --}}
                        <a class="btn bg-olive" href="{{ route('customers.payments') }}">
                            <i class="fas fa-history"></i>
                            <h6> {{ getLocaleString($operator->id, 'Payment History') }} </h6>
                        </a>
                        {{-- Payment History --}}
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card text-center">
                    <div class="card-body">
                        {{-- Complaints --}}
                        <a class="btn bg-teal" href="{{ route('complaints-customer-interface.index') }}">
                            <i class="fas fa-mail-bulk"></i>
                            <h6> {{ getLocaleString($operator->id, 'Complaints') }} </h6>
                        </a>
                        {{-- Complaints --}}
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection

@section('pageJs')
@endsection
