@extends ('laraview.layouts.topNavLayout')

@section('title')
    Buy Package
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
                $active_link = '1';
            @endphp
            @include('customers.nav-links')
        </div>
        {{-- Navigation bar --}}

        <nav class="navbar navbar-light bg-light justify-content-end">

            <form class="form-inline" method="GET" action="{{ route('customers.packages') }}">

                {{-- sort --}}
                <div class="form-group mr-sm-2">
                    <select name="sort" id="sort" class="form-control">
                        <option value=''>sort by...</option>
                        <option value='price'>price</option>
                        <option value='validity'>validity</option>
                    </select>
                </div>
                {{-- sort --}}

                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">FILTER</button>

            </form>

        </nav>

        <div class="row">

            @foreach ($packages as $package)
                <div class="col-sm-4">

                    <div class="position-relative p-3 border border-secondary">

                        <div class="ribbon-wrapper ribbon-lg">
                            <div class="ribbon bg-danger">
                                Price: {{ $package->price }} {{ config('consumer.currency') }}
                            </div>
                        </div>

                        <ul class="list-group list-group-flush">

                            <button type="button" class="list-group-item list-group-item-action active">
                                {{ $package->name }}
                            </button>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Speed Limit
                                @if ($package->master_package->rate_limit)
                                    <span class="badge badge-primary badge-pill">{{ $package->master_package->rate_limit }}
                                        {{ $package->master_package->readable_rate_unit }}</span>
                                @else
                                    <span class="badge badge-primary badge-pill">Unlimited</span>
                                @endif
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Volume Limit
                                @if ($package->master_package->volume_limit)
                                    <span
                                        class="badge badge-primary badge-pill">{{ $package->master_package->volume_limit }}
                                        {{ $package->volume_unit }}</span>
                                @else
                                    <span class="badge badge-primary badge-pill">Unlimited MB</span>
                                @endif
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Validity
                                <span class="badge badge-primary badge-pill">{{ $package->master_package->validity }} Days
                                </span>
                            </li>

                            @if ($package->fair_usage_policy)
                                <li class="list-group-item">
                                    Note: If the data usage exceeds <span class="font-weight-bold">
                                        {{ $package->fair_usage_policy->data_limit }} GB </span>,
                                    the speed limit will drop to <span class="font-weight-bold">
                                        {{ $package->fair_usage_policy->speed_limit }} Mbps </span>
                                </li>
                            @endif

                            <li class="list-group-item d-flex justify-content-between align-items-center">

                                <form class="form-inline" method="get"
                                    action="{{ route('customers.purchase-package', ['package' => $package]) }}"
                                    onsubmit="return disableDuplicateSubmit()">

                                    <div class="form-group">

                                        <label for="payment_gateway_id" class="sr-only">Payment Gateway</label>

                                        <select id="payment_gateway_id" name="payment_gateway_id"
                                            class="form-control form-control-sm" required>
                                            <option value="">Pay With...</option>

                                            @if ($payment_gateways)
                                                @foreach ($payment_gateways as $payment_gateway)
                                                    <option value="{{ $payment_gateway->id }}">
                                                        {{ $payment_gateway->payment_method }}
                                                    </option>
                                                @endforeach
                                            @endif

                                        </select>

                                    </div>

                                    <button type="submit" id="submit-button"
                                        class="btn btn-danger btn-sm">{{ getLocaleString($operator->id, 'BUY') }}</button>

                                </form>

                            </li>

                        </ul>

                    </div>

                </div>
            @endforeach

        </div>

        @include('customers.footer-nav-links')

    </div>
@endsection

@section('pageJs')
@endsection
