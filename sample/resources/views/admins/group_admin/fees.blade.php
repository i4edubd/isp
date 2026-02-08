@extends ('laraview.layouts.sideNavLayout')

@section('title')
subscription policies
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '9';
$active_link = '3';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">Subscriptions</li>
    <li class="breadcrumb-item active">Pricing</li>
</ol>
@endsection

@section('content')

<div class="card card-outline card-info">
    <div class="card-body">
        <div class="row">
            <div class="col-sm">
                <form class="form-inline" method="GET" action="{{ route('software.pricing') }}">
                    <div class="form-group mx-sm-3 mb-2">
                        <label for="user_count" class="sr-only">User Count</label>
                        <input type="number" name="user_count" class="form-control" id="user_count"
                            placeholder="user count" required>
                    </div>
                    <button type="submit" class="btn btn-dark mb-2">Calculate</button>
                </form>
            </div>
            <div class="col-sm">
                @if ($calculate_result)
                Calculated results:
                <s> {{ $calculate_result->get('calculated_price') }} </s>
                {{ $calculate_result->get('amount') }}
                {{ $calculate_result->get('currency_code') }}
                @endif
            </div>
        </div>
    </div>
</div>

<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="pills-subscription-tab" data-toggle="pill" href="#pills-subscription" role="tab"
            aria-controls="pills-subscription" aria-selected="true">Subscription Fee</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-support_pricing-tab" data-toggle="pill" href="#pills-support_pricing" role="tab"
            aria-controls="pills-support_pricing" aria-selected="false">Support Pricing</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="pills-other_fee-tab" data-toggle="pill" href="#pills-other_fee" role="tab"
            aria-controls="pills-other_fee" aria-selected="false">Other Fee</a>
    </li>
</ul>

<div class="tab-content" id="pills-tabContent">

    <div class="tab-pane fade show active" id="pills-subscription" role="tabpanel"
        aria-labelledby="pills-subscription-tab">

        <div class="card">
            <div class="card-body">
                <div class="row">

                    {{-- P#1 (1-10 user) | max 50 --}}
                    @php
                    $minFee = getSubscriptionPrice($operator->id, 1);
                    $maxFee = getSubscriptionPrice($operator->id, 10);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    P1 :
                                    <s> {{ $maxFee->get('calculated_price') }} </s>
                                    {{ $maxFee->get('amount') }}
                                    {{ $maxFee->get('currency_code') }}
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <div class="p-2">
                                    User Limit: <span class="badge badge-pill badge-info"> 1-10 </span>
                                </div>
                                <div class="p-2">
                                    Per User Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('per_user_fee') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Minimum Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('amount') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Maximum Fee: <span class="badge badge-pill badge-info">
                                        {{ $maxFee->get('amount') }}
                                        {{ $maxFee->get('currency_code') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- P#1 (1-10 user) | max 50 --}}

                    {{-- P#2 (11-50 user) | max 200 --}}
                    @php
                    $minFee = getSubscriptionPrice($operator->id, 11);
                    $maxFee = getSubscriptionPrice($operator->id, 50);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    P2 :
                                    <s> {{ $maxFee->get('calculated_price') }} </s>
                                    {{ $maxFee->get('amount') }}
                                    {{ $maxFee->get('currency_code') }}
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <div class="p-2">
                                    User Limit: <span class="badge badge-pill badge-info"> 11-50 </span>
                                </div>
                                <div class="p-2">
                                    Per User Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('per_user_fee') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Minimum Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('amount') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Maximum Fee: <span class="badge badge-pill badge-info">
                                        {{ $maxFee->get('amount') }}
                                        {{ $maxFee->get('currency_code') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- P#2 (11-50 user) | max 200 --}}

                    {{-- P#3 (51-170 user) | max 500 --}}
                    @php
                    $minFee = getSubscriptionPrice($operator->id, 51);
                    $maxFee = getSubscriptionPrice($operator->id, 170);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    P3 :
                                    <s> {{ $maxFee->get('calculated_price') }} </s>
                                    {{ $maxFee->get('amount') }}
                                    {{ $maxFee->get('currency_code') }}
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <div class="p-2">
                                    User Limit: <span class="badge badge-pill badge-info"> 51-170 </span>
                                </div>
                                <div class="p-2">
                                    Per User Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('per_user_fee') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Minimum Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('amount') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Maximum Fee: <span class="badge badge-pill badge-info">
                                        {{ $maxFee->get('amount') }}
                                        {{ $maxFee->get('currency_code') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- P#3 (51-170 user) | max 200 --}}

                </div>

                <div class="row mt-4">

                    {{-- P#4 (171-500 user) | max 1000 --}}
                    @php
                    $minFee = getSubscriptionPrice($operator->id, 171);
                    $maxFee = getSubscriptionPrice($operator->id, 500);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    P4 :
                                    <s> {{ $maxFee->get('calculated_price') }} </s>
                                    {{ $maxFee->get('amount') }}
                                    {{ $maxFee->get('currency_code') }}
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <div class="p-2">
                                    User Limit: <span class="badge badge-pill badge-info"> 171-500 </span>
                                </div>
                                <div class="p-2">
                                    Per User Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('per_user_fee') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Minimum Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('amount') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Maximum Fee: <span class="badge badge-pill badge-info">
                                        {{ $maxFee->get('amount') }}
                                        {{ $maxFee->get('currency_code') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- P#4 (171-500 user) | max 1000 --}}

                    {{-- P#5 (501-1000 user) | max 1500 --}}
                    @php
                    $minFee = getSubscriptionPrice($operator->id, 501);
                    $maxFee = getSubscriptionPrice($operator->id, 1000);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    P5 :
                                    <s> {{ $maxFee->get('calculated_price') }} </s>
                                    {{ $maxFee->get('amount') }}
                                    {{ $maxFee->get('currency_code') }}
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <div class="p-2">
                                    User Limit: <span class="badge badge-pill badge-info"> 501-1000 </span>
                                </div>
                                <div class="p-2">
                                    Per User Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('per_user_fee') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Minimum Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('amount') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Maximum Fee: <span class="badge badge-pill badge-info">
                                        {{ $maxFee->get('amount') }}
                                        {{ $maxFee->get('currency_code') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- P#5 (501-1000 user) | max 1500 --}}

                    {{-- P#6 (1001-1700 user) | max 2000 --}}
                    @php
                    $minFee = getSubscriptionPrice($operator->id, 1001);
                    $maxFee = getSubscriptionPrice($operator->id, 1700);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    P6 :
                                    <s> {{ $maxFee->get('calculated_price') }} </s>
                                    {{ $maxFee->get('amount') }}
                                    {{ $maxFee->get('currency_code') }}
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <div class="p-2">
                                    User Limit: <span class="badge badge-pill badge-info"> 1001-1700 </span>
                                </div>
                                <div class="p-2">
                                    Per User Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('per_user_fee') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Minimum Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('amount') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Maximum Fee: <span class="badge badge-pill badge-info">
                                        {{ $maxFee->get('amount') }}
                                        {{ $maxFee->get('currency_code') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- P#6 (1001-1700 user) | max 2000 --}}

                </div>

                <div class="row mt-4">

                    {{-- P#7 (1701-3000 user) | max 3000 --}}
                    @php
                    $minFee = getSubscriptionPrice($operator->id, 1701);
                    $maxFee = getSubscriptionPrice($operator->id, 3000);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    P7 :
                                    <s> {{ $maxFee->get('calculated_price') }} </s>
                                    {{ $maxFee->get('amount') }}
                                    {{ $maxFee->get('currency_code') }}
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <div class="p-2">
                                    User Limit: <span class="badge badge-pill badge-info"> 1701-3000 </span>
                                </div>
                                <div class="p-2">
                                    Per User Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('per_user_fee') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Minimum Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('amount') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Maximum Fee: <span class="badge badge-pill badge-info">
                                        {{ $maxFee->get('amount') }}
                                        {{ $maxFee->get('currency_code') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- P#7 (1701-3000 user) | max 3000 --}}

                    {{-- P#8 (3001-4000 user) | max 3600 --}}
                    @php
                    $minFee = getSubscriptionPrice($operator->id, 3001);
                    $maxFee = getSubscriptionPrice($operator->id, 4000);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    P8 :
                                    <s> {{ $maxFee->get('calculated_price') }} </s>
                                    {{ $maxFee->get('amount') }}
                                    {{ $maxFee->get('currency_code') }}
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <div class="p-2">
                                    User Limit: <span class="badge badge-pill badge-info"> 3001-4000 </span>
                                </div>
                                <div class="p-2">
                                    Per User Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('per_user_fee') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Minimum Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('amount') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Maximum Fee: <span class="badge badge-pill badge-info">
                                        {{ $maxFee->get('amount') }}
                                        {{ $maxFee->get('currency_code') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- P#8 (3001-4000 user) | max 3600 --}}

                    {{-- P#9 (4001-5000 user) | max 4000 --}}
                    @php
                    $minFee = getSubscriptionPrice($operator->id, 4001);
                    $maxFee = getSubscriptionPrice($operator->id, 5000);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    P9 :
                                    <s> {{ $maxFee->get('calculated_price') }} </s>
                                    {{ $maxFee->get('amount') }}
                                    {{ $maxFee->get('currency_code') }}
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <div class="p-2">
                                    User Limit: <span class="badge badge-pill badge-info"> 4001-5000 </span>
                                </div>
                                <div class="p-2">
                                    Per User Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('per_user_fee') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Minimum Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('amount') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Maximum Fee: <span class="badge badge-pill badge-info">
                                        {{ $maxFee->get('amount') }}
                                        {{ $maxFee->get('currency_code') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- P#9 (4001-5000 user) | max 4000 --}}

                </div>


                <div class="row mt-4">

                    {{-- P#10 (5001-6000 user) | max 4200 --}}
                    @php
                    $minFee = getSubscriptionPrice($operator->id, 5001);
                    $maxFee = getSubscriptionPrice($operator->id, 6000);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    P10 :
                                    <s> {{ $maxFee->get('calculated_price') }} </s>
                                    {{ $maxFee->get('amount') }}
                                    {{ $maxFee->get('currency_code') }}
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <div class="p-2">
                                    User Limit: <span class="badge badge-pill badge-info"> 5001-6000 </span>
                                </div>
                                <div class="p-2">
                                    Per User Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('per_user_fee') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Minimum Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('amount') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Maximum Fee: <span class="badge badge-pill badge-info">
                                        {{ $maxFee->get('amount') }}
                                        {{ $maxFee->get('currency_code') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- P#10 (5001-6000 user) | max 4200 --}}

                    {{-- P#11 (6001-8000 user) | max 4800 --}}
                    @php
                    $minFee = getSubscriptionPrice($operator->id, 6001);
                    $maxFee = getSubscriptionPrice($operator->id, 8000);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    P11 :
                                    <s> {{ $maxFee->get('calculated_price') }} </s>
                                    {{ $maxFee->get('amount') }}
                                    {{ $maxFee->get('currency_code') }}
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <div class="p-2">
                                    User Limit: <span class="badge badge-pill badge-info"> 6001-8000 </span>
                                </div>
                                <div class="p-2">
                                    Per User Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('per_user_fee') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Minimum Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('amount') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Maximum Fee: <span class="badge badge-pill badge-info">
                                        {{ $maxFee->get('amount') }}
                                        {{ $maxFee->get('currency_code') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- P#11 (6001-8000 user) | max 4800 --}}

                    {{-- P#12 (8001-10000 user) | max 5000 --}}
                    @php
                    $minFee = getSubscriptionPrice($operator->id, 8001);
                    $maxFee = getSubscriptionPrice($operator->id, 10000);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    P12 :
                                    <s> {{ $maxFee->get('calculated_price') }} </s>
                                    {{ $maxFee->get('amount') }}
                                    {{ $maxFee->get('currency_code') }}
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <div class="p-2">
                                    User Limit: <span class="badge badge-pill badge-info"> 8001-10000 </span>
                                </div>
                                <div class="p-2">
                                    Per User Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('per_user_fee') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Minimum Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('amount') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Maximum Fee: <span class="badge badge-pill badge-info">
                                        {{ $maxFee->get('amount') }}
                                        {{ $maxFee->get('currency_code') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- P#12 (8001-10000 user) | max 5000 --}}

                </div>


                <div class="row mt-4">

                    {{-- P#13 (10001-∞ user) | max ∞ --}}
                    @php
                    $minFee = getSubscriptionPrice($operator->id, 10001);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    P13 : ∞ {{ $minFee->get('currency_code') }}
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <div class="p-2">
                                    User Limit: <span class="badge badge-pill badge-info"> 10001-Unlimited </span>
                                </div>
                                <div class="p-2">
                                    Per User Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('per_user_fee') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Minimum Fee: <span class="badge badge-pill badge-info">
                                        {{ $minFee->get('amount') }}
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                                <div class="p-2">
                                    Maximum Fee: <span class="badge badge-pill badge-info">
                                        ∞
                                        {{ $minFee->get('currency_code') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- P#13 (10001-∞ user) | max ∞ --}}

                </div>

            </div>
            <!-- /card-body -->
        </div>
        <!-- /card -->

    </div>

    <div class="tab-pane fade" id="pills-other_fee" role="tabpanel" aria-labelledby="pills-other_fee-tab">


        <div class="card">
            <div class="card-body">
                <div class="row">

                    {{-- Registration Fee --}}
                    @php
                    $currency = getCurrency($operator->id);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    @if ($currency == 'USD')
                                    <s> 100 </s>
                                    10
                                    @else
                                    <s> 1000 </s>
                                    100
                                    @endif
                                    {{ $currency }}
                                </div>
                            </div>
                            <h3> Registration Fee </h3>
                        </div>
                    </div>
                    {{-- Registration Fee --}}

                    {{-- Payment Gateway Integration Cost --}}
                    @php
                    $currency = getCurrency($operator->id);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    @if ($currency == 'USD')
                                    <s> 50 </s>
                                    20
                                    @else
                                    2000
                                    @endif
                                    {{ $currency }}
                                </div>
                            </div>
                            <h3> Payment Gateway <br> Integration Cost </h3>
                        </div>
                    </div>
                    {{-- Payment Gateway Integration Cost --}}

                    {{-- SMS Gateway Integration Cost --}}
                    @php
                    $currency = getCurrency($operator->id);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    @if ($currency == 'USD')
                                    <s> 50 </s>
                                    20
                                    @else
                                    1000
                                    @endif
                                    {{ $currency }}
                                </div>
                            </div>
                            <h3> SMS Gateway <br> Integration Cost </h3>
                        </div>
                    </div>
                    {{-- SMS Gateway Integration Cost --}}
                </div>

            </div>
            <!-- /card-body -->
        </div>
        <!-- /card -->

    </div>

    <div class="tab-pane fade" id="pills-support_pricing" role="tabpanel" aria-labelledby="pills-support_pricing-tab">


        <div class="card">
            <div class="card-body">
                <div class="row">

                    {{-- Free --}}
                    @php
                    $currency = getCurrency($operator->id);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    Free: 0
                                    {{ $currency }}
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <div class="p-2">
                                    WhatsApp - Text Messaging
                                </div>
                                <div class="p-2">
                                    10.00 AM to 5.00 PM
                                </div>
                                <div class="p-2">
                                    Response Time - within 24 hours
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Free --}}

                    {{-- Basic --}}
                    @php
                    $currency = getCurrency($operator->id);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    Basic:
                                    @if ($currency == 'USD')
                                    2
                                    @else
                                    100
                                    @endif
                                    {{ $currency }} Hourly
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <div class="p-2">
                                    WhatsApp - Text Messaging & Voice Call
                                </div>
                                <div class="p-2">
                                    Remote Desktop Support
                                </div>
                                <div class="p-2">
                                    10.00 AM to 10.00 PM
                                </div>
                                <div class="p-2">
                                    Response Time - within 6 hours
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Basic --}}

                    {{-- Gold --}}
                    @php
                    $currency = getCurrency($operator->id);
                    @endphp
                    <div class="col-sm-4 pb-2">
                        <div class="position-relative p-3 bg-gray" style="height: 180px">
                            <div class="ribbon-wrapper ribbon-xl">
                                <div class="ribbon bg-warning">
                                    Gold:
                                    @if ($currency == 'USD')
                                    20
                                    @else
                                    1000
                                    @endif
                                    {{ $currency }} Hourly
                                </div>
                            </div>
                            <div class="d-flex flex-column mb-3">
                                <div class="p-2">
                                    WhatsApp - Text Messaging & Voice Call
                                </div>
                                <div class="p-2">
                                    Remote Desktop Support
                                </div>
                                <div class="p-2">
                                    24/7/365
                                </div>
                                <div class="p-2">
                                    Response Time - within 1 hour
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Gold --}}

                </div>

            </div>
            <!-- /card-body -->
        </div>
        <!-- /card -->

    </div>

</div>

@endsection

@section('pageJs')
@endsection