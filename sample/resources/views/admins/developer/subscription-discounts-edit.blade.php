@extends ('laraview.layouts.sideNavLayout')

@section('title')
Discount Edit
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '6';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection

@section('contentTitle')
<h3>Edit Discount</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>

    <form id="quickForm" method="POST"
        action="{{ route('subscription_discounts.update',['subscription_discount' => $subscription_discount->id ]) }}">
        @csrf
        @method('put')

        <div class="card-body">
            <div class="row">
                <div class="col-sm">

                    <!--operator_id-->
                    <div class="form-group">
                        <label for="operator_id">operator</label>
                        <input class="form-control" id="operator_id" type="text"
                            placeholder="{{ $subscription_discount->operator->company }} {{ $subscription_discount->operator->role }}"
                            readonly>
                    </div>
                    <!--/operator_id-->


                    <!--amount-->
                    <div class="form-group">
                        <label for="amount">amount</label>
                        <input name="amount" type="text" class="form-control @error('amount') is-invalid @enderror"
                            id="amount" value="{{ $subscription_discount->amount }}">
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
