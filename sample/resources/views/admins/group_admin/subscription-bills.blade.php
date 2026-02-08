@extends ('laraview.layouts.sideNavLayout')

@section('title')
Subscription Bills
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
<div class="d-flex flex-wrap">

    <h3 class="mr-4">Subscription Bills</h3>

    <a class="btn btn-outline-dark" href="https://ispbills.com/pricing/" role="button">View pricing </a>

</div>
@endsection

@section('content')

<div class="row">

    @foreach ($subscription_bills as $subscription_bill )

    <div class="col-sm-6">
        <div class="card">
            <div class="card-body">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item"><span class="font-weight-bold">Bill To:</span>
                        {{ $subscription_bill->operator_name }}
                    </li>

                    <li class="list-group-item"><span class="font-weight-bold">Customer Count :</span>
                        {{ $subscription_bill->user_count }}
                    </li>

                    <li class="list-group-item"><span class="font-weight-bold">Amount :</span>
                        @if ($subscription_bill->calculated_price > $subscription_bill->amount)
                        <del>{{ $subscription_bill->calculated_price }} {{ config('consumer.currency') }} </del> {{
                        $subscription_bill->amount }}
                        {{ config('consumer.currency') }}
                        @else
                        {{ $subscription_bill->amount }} {{ config('consumer.currency') }}
                        @endif
                    </li>

                    <li class="list-group-item"><span class="font-weight-bold">Month : </span>
                        {{ $subscription_bill->month }}
                    </li>

                    <li class="list-group-item"><span class="font-weight-bold">Year :</span>
                        {{ $subscription_bill->year }}
                    </li>

                </ul>

                <hr>

                <form class="form-inline" method="get"
                    action="{{ route('subscription_payments.create',['subscription_bill' => $subscription_bill->id]) }}">

                    <div class="form-group">
                        <label for="payment_gateway_id" class="sr-only">Payment Gateway</label>

                        <select id="payment_gateway_id" name="payment_gateway_id" class="form-control form-control-sm"
                            required>
                            <option value="">Pay With...</option>

                            @if ($subscription_bill->payment_gateways)

                            @foreach ($subscription_bill->payment_gateways as $payment_gateway)

                            <option value="{{ $payment_gateway->id }}">{{ $payment_gateway->payment_method }}</option>

                            @endforeach

                            @endif

                        </select>

                    </div>

                    <button type="submit" class="btn btn-danger btn-sm">SUBMIT</button>

                </form>

            </div>

        </div>

    </div>
    <!--/col-sm-6-->

    @endforeach

</div>
<!--/row-->

@endsection

@section('pageJs')
@endsection