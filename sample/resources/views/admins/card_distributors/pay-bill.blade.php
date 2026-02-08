@extends ('laraview.layouts.sideNavLayoutForCardDistributors')

@section('title')
    Pay Bill
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
                Pay Bill
            </div>

            <p> <span class="card-text font-italic font-weight-bold">Customer Name: </span> {{ $customer->name }}</p>
            <p> <span class="card-text font-italic font-weight-bold">Customer Mobile: </span> {{ $customer->mobile }}</p>
            <p> <span class="card-text font-italic font-weight-bold">Customer Status: </span> {{ $customer->status }}</p>
            <p> <span class="card-text font-italic font-weight-bold">Bill Amount: </span> {{ $bill_amount }}
                {{ $currency }}
            </p>

            <form method="POST" action="{{ route('card.customer.pay-bill.store', ['customer_id' => $customer->id]) }}"
                onsubmit="return disableDuplicateSubmit()">
                @csrf

                <button type="submit" id="submit-button" class="btn btn-primary">Pay</button>

            </form>

        </div>

    </div>
@endsection

@section('pageJs')
@endsection
