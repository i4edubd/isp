@extends ('laraview.layouts.sideNavLayout')

@section('title')
Subscription Payment
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '9';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
<h3>Subscription Payment</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body text-center">

        <div class="card-header bg-info">
            Pay Subscription Payment With {{ $payment_gateway->send_money_provider }} (Send Money)
        </div>

        <p class="card-text font-italic font-weight-bold">Amount: {{ $subscription_payment->amount_paid }}
            {{ config('consumer.currency') }}
        </p>

        <p class="card-text">
            Payment Method: <i class="far fa-paper-plane text-danger"></i>
            Send Money ({{ $payment_gateway->send_money_provider }})
        </p>

        <p class="card-text">
            {{ $payment_gateway->send_money_provider }} Number: <i class="fas fa-mobile-alt text-danger"></i>
            {{ $payment_gateway->msisdn }} (personal)
        </p>

        @if (config('consumer.can_submit_send_money'))
        <form method="POST"
            action="{{ route('send_money.subscription_payment.store', ['subscription_payment' => $subscription_payment->id]) }}">

            @csrf

            <div class="form-row">

                <div class="form-group col-md-4">
                </div>

                {{-- card_number --}}
                <div class="form-group col-md-2">
                    <label for="card_number" class="sr-only">From Number</label>
                    <input type="text" class="form-control text-center" id="card_number" name="card_number"
                        placeholder="From Number" required>
                </div>
                {{-- card_number --}}

                {{-- bank_txnid --}}
                <div class="form-group col-md-2">
                    <label for="bank_txnid" class="sr-only">Transaction ID</label>
                    <input type="text" class="form-control text-center" id="bank_txnid" name="bank_txnid"
                        placeholder="Transaction ID" required>
                </div>
                {{-- bank_txnid --}}

                <div class="form-group col-md-4">
                </div>

            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        @endif

    </div>

</div>

@endsection

@section('pageJs')
@endsection