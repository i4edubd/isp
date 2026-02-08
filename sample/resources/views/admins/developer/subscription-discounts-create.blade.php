@extends ('laraview.layouts.sideNavLayout')

@section('title')
Discounts
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
<h3>New Discount</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>

    <form id="quickForm" method="POST" action="{{ route('subscription_discounts.store') }}">
        @csrf

        <div class="card-body">
            <div class="row">
                <div class="col-sm">
                    <!--operator_id-->
                    <div class="form-group">
                        <label for="operator_id"><span class="text-danger">*</span>operator</label>
                        <select class="form-control" id="operator_id" name="operator_id" required>
                            <option value="">Please select... </option>
                            @foreach ($operators as $operator)
                            <option value="{{ $operator->id }}">{{ $operator->id }}, {{ $operator->company }} , {{
                                $operator->role }} </option>
                            @endforeach
                        </select>
                    </div>
                    <!--/operator_id-->


                    <!--amount-->
                    <div class="form-group">
                        <label for="amount"><span class="text-danger">*</span>Amount</label>
                        <input name="amount" type="text" class="form-control @error('amount') is-invalid @enderror"
                            id="amount" value="{{ old('amount') }}" required>
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
