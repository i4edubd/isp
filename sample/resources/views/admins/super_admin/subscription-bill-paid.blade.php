@extends ('laraview.layouts.sideNavLayout')

@section('title')
Subscription Bills
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '3';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.super_admin.sidebar')
@endsection

@section('contentTitle')
<h3>Pay Subscription Bills</h3>
@endsection

@section('content')

<div class="card-body">

    <div class="row">

        <div class="col-sm-6">
            <div class="card">
                <div class="card-body">

                    <ul class="list-group list-group-flush">

                        <li class="list-group-item"><span class="font-weight-bold"> To:</span>
                            {{ $subscription_bill->operator_name }}
                        </li>

                        <li class="list-group-item"><span class="font-weight-bold">Customer Count :</span>
                            {{ $subscription_bill->user_count }}
                        </li>

                        <li class="list-group-item"><span class="font-weight-bold">Amount :</span>
                            {{ $subscription_bill->amount }} {{ config('consumer.currency') }}
                        </li>

                        <li class="list-group-item"><span class="font-weight-bold">Month : </span>
                            {{ $subscription_bill->month }}
                        </li>

                        <li class="list-group-item"><span class="font-weight-bold">Year :</span>
                            {{ $subscription_bill->year }}
                        </li>

                    </ul>

                    <hr>

                    <form method="POST"
                        action="{{ route('subscription_bill.paid.store', ['subscription_bill' => $subscription_bill->id]) }}">

                        @csrf

                        <div class="form-group">
                            <label for="bank_txnid">Transaction ID</label>
                            <input type="text" class="form-control" id="bank_txnid" name="bank_txnid"
                                placeholder="Transaction ID">
                        </div>

                        <button type="submit" class="btn btn-danger btn-sm">SUBMIT</button>

                    </form>

                </div>

            </div>

        </div>
        <!--/col-sm-6-->

    </div>
    <!--/row-->

</div>

@endsection

@section('pageJs')
@endsection