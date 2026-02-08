@extends ('laraview.layouts.sideNavLayout')

@section('title')
Account Balance
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '1';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection


@section('contentTitle')
<h3>Add Account Balance</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <div class="col-sm-6">

            <form id="quickForm" method="POST"
                action="{{ route('operators.account-balance.store', ['operator' => $operator->id]) }}">

                @csrf

                {{-- operator --}}
                <div class="form-group">
                    <label for="disabledTextInput">operator</label>
                    <input type="text" id="disabledTextInput" class="form-control" placeholder="{{ $operator->name }}"
                        disabled>
                </div>
                {{-- operator --}}

                <!--amount-->
                <div class="form-group">
                    <label for="amount">Amount</label>

                    <div class="input-group">
                        <input name="amount" type="text" class="form-control @error('amount') is-invalid @enderror"
                            id="amount" required>
                        <div class="input-group-append">
                            <span class="input-group-text">{{ config('consumer.currency') }}</span>
                        </div>
                    </div>

                    @error('amount')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror

                </div>
                <!--/amount-->

                 <!--note-->
                 <div class="form-group">
                    <label for="note">Note</label>
                    <input name="note" type="text" class="form-control @error('note') is-invalid @enderror"
                        id="note" value="{{ old('note') }}">
                    @error('note')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <!--/note-->

                <button type="submit" class="btn btn-primary mt-2">Submit</button>

            </form>

        </div>

    </div>

</div>

@endsection

@section('pageJs')
@endsection
