@extends ('laraview.layouts.topNavLayout')

@section('title')
    Recharge With Cards
@endsection

@section('pageCss')
@endsection

@section('company')
    {{ $operator->company }}
@endsection

@section('topNavbar')
@endsection

@section('contentTitle')
    <a class="btn btn-primary" href="{{ route('customers.card-stores') }}" role="button">
        <i class="fas fa-store"></i> Card Stores
    </a>
@endsection

@section('content')
    <div class="card">

        <div class="card-body text-center">

            <div class="card-header bg-info">
                Pay With Recharge Card
            </div>

            <p class="card-text font-italic font-weight-bold">Package Name: {{ $package->name }}</p>

            <p class="card-text font-italic font-weight-bold">Amount: {{ $package->price }}
                {{ getCurrency($operator->id) }}
            </p>

            <p class="card-text">Customer: {{ $customer_payment->mobile }}</p>

            @if ($package->master_package->rate_limit)
                <p class="card-text">Speed Limit: {{ $package->master_package->rate_limit }}
                    {{ $package->master_package->readable_rate_unit }}</p>
            @else
                <p class="card-text">Speed Limit: Unlimited</p>
            @endif

            @if ($package->volume_limit)
                <p class="card-text">MB Limit: {{ $package->master_package->volume_limit }}
                    {{ $package->master_package->volume_unit }}</p>
            @else
                <p class="card-text">MB Limit: Unlimited MB</p>
            @endif

            <p class="card-text">Validity: {{ $package->master_package->validity }} Days</p>

            <p class="card-text">Payment Method: Recharge Card</p>

            <form method="POST"
                action="{{ route('customer_payments.recharge-cards.store', ['customer_payment' => $customer_payment->id]) }}"
                onsubmit="return disableDuplicateSubmit()">
                @csrf

                <div class="form-row">

                    <div class="form-group col-md-5">
                    </div>

                    {{-- pin --}}
                    <div class="form-group col-md-2">
                        <label for="pin" class="sr-only">PIN Number</label>
                        <input type="text" class="form-control text-center" id="pin" name="pin"
                            placeholder="PIN Number" required>
                    </div>
                    {{-- pin --}}

                    <div class="form-group col-md-5">
                    </div>

                </div>

                <button type="submit" id="submit-button" class="btn btn-primary">Submit</button>

            </form>

        </div>

    </div>
@endsection

@section('pageJs')
@endsection
