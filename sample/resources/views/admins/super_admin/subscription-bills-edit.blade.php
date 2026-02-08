@extends ('laraview.layouts.sideNavLayout')

@section('title')
Subscription Bill Edit
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
<h3>Edit Subscription Bill</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <p class="text-danger">* required field</p>

        <form id="quickForm" method="POST"
            action="{{ route('subscription_bills.update', ['subscription_bill' => $subscription_bill->id ]) }}">
            @csrf
            @method('PUT')

            <!--amount-->
            <div class="form-group">
                <label for="amount"><span class="text-danger">*</span>Amount</label>
                <input name="amount" type="text" class="form-control @error('amount') is-invalid @enderror" id="amount"
                    value="{{ $subscription_bill->amount }}" required>
                @error('amount')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <!--/amount-->

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            <!--/card-footer-->

        </form>

    </div>

</div>

@endsection

@section('pageJs')
@endsection
