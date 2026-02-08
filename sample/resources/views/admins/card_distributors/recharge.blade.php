@extends ('laraview.layouts.sideNavLayoutForCardDistributors')

@section('title')
    Recharge Customer
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
                Recharge
            </div>

            <p> <span class="card-text font-italic font-weight-bold">Customer Name: </span> {{ $customer->name }}</p>
            <p> <span class="card-text font-italic font-weight-bold">Customer Mobile: </span> {{ $customer->mobile }}</p>
            <p> <span class="card-text font-italic font-weight-bold">Customer Status: </span> {{ $customer->status }}</p>
            <p> <span class="card-text font-italic font-weight-bold">Active Package: </span> {{ $customer->package_name }}
            </p>
            <p> <span class="card-text font-italic font-weight-bold">Valid Until: </span> {{ $customer->package_expired_at }}
            </p>

            <form method="POST" action="{{ route('card.customer.recharge.store', ['customer_id' => $customer->id]) }}"
                onsubmit="return disableDuplicateSubmit()">
                @csrf

                <div class="form-row align-items-center">
                    <div class="col-4"></div>

                    <!--package_id-->
                    <div class="form-group col-4 mb-2">
                        <label for="package_id"><span class="text-danger">*</span>Select Package</label>
                        <select class="form-control" id="package_id" name="package_id" required>
                            @foreach ($packages->sortBy('price') as $package)
                                <option value="{{ $package->id }}">{{ $package->name }} :: {{ $package->price }}
                                    {{ $currency }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!--/package_id-->

                    <div class="col-4"></div>
                </div>

                <button type="submit" id="submit-button" class="btn btn-primary">Submit</button>

            </form>

        </div>

    </div>
@endsection

@section('pageJs')
@endsection
