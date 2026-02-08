@extends ('laraview.layouts.sideNavLayout')

@section('title')
Credit Limit
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '10';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection


@section('contentTitle')
<h3>Edit Credit Limit</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <div class="col-sm-6">

            <form id="quickForm" method="POST"
                action="{{ route('sub_operators.credit-limit.store', ['operator' => $operator->id]) }}">

                @csrf

                {{-- operator --}}
                <div class="form-group">
                    <label for="disabledTextInput">operator</label>
                    <input type="text" id="disabledTextInput" class="form-control" placeholder="{{ $operator->name }}"
                        disabled>
                </div>
                {{-- operator --}}

                <!--credit_limit-->
                <div class="form-group">
                    <label for="credit_limit">Credit Limit (Enter 0 for no limit)</label>

                    <div class="input-group">
                        <input name="credit_limit" type="text"
                            class="form-control @error('credit_limit') is-invalid @enderror" id="credit_limit"
                            value="{{ $operator->credit_limit }}" required>
                        <div class="input-group-append">
                            <span class="input-group-text">{{ config('consumer.currency') }}</span>
                        </div>
                    </div>

                    @error('credit_limit')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror

                </div>
                <!--/credit_limit-->

                <button type="submit" class="btn btn-primary mt-2">Submit</button>

            </form>

        </div>

    </div>

</div>

@endsection

@section('pageJs')
@endsection
