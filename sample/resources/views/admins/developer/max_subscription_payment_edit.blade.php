@extends ('laraview.layouts.sideNavLayout')

@section('title')
Max Payment Edit
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '9';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection


@section('contentTitle')
<h3>Edit Max Amount</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>
    <form id="quickForm" method="POST"
        action="{{ route('max_subscription_payments.update', ['max_subscription_payment' => $max_subscription_payment->id ]) }}">
        @csrf
        @method('put')

        <div class="card-body">
            <div class="row">
                <div class="col-sm">

                    <!--operator_id-->
                    <div class="form-group">
                        <label for="operator_id">operator</label>
                        <input class="form-control" id="operator_id" type="text"
                            placeholder="{{ $max_subscription_payment->operator->company }} {{ $max_subscription_payment->operator->role }}"
                            readonly>
                    </div>
                    <!--/operator_id-->


                    <!--amount-->
                    <div class="form-group">
                        <label for="amount">amount</label>
                        <input name="amount" type="text" class="form-control @error('amount') is-invalid @enderror"
                            id="amount" value="{{ $max_subscription_payment->amount }}">
                    </div>
                    <!--/amount-->

                </div>
                <!--/Left Column-->

            </div>
            <!--/row-->

        </div>
        <!--/card-body-->

        <div class="card-footer">
            <button type="submit" class="btn btn-dark">Submit</button>
        </div>
        <!--/card-footer-->

    </form>

</div>

@endsection

@section('pageJs')
@endsection
