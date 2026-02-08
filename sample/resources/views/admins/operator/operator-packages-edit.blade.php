@extends ('laraview.layouts.sideNavLayout')

@section('title')
Edit Package
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
<h3>Edit Package</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title font-weight-bold">Reseller: {{ $operator->name }}</h3>
    </div>

    <div class="card-body">

        <p class="text-danger">* required field</p>

        <form id="quickForm" method="POST"
            action="{{ route('operators.packages.update', ['operator' => $operator->id, 'package' => $package->id]) }}">

            @csrf

            @method('PUT')

            <div class="col-sm-6">

                <!--name-->
                <div class="form-group">
                    <label for="name"><span class="text-danger">*</span>Name</label>
                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        value="{{ $package->name }}" required>

                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <!--/name-->

                <!--price-->
                <div class="form-group">
                    <label for="price"><span class="text-danger">*</span>Customer's Price</label>

                    <div class="input-group">
                        <input name="price" type="number" class="form-control @error('price') is-invalid @enderror"
                            id="price" value="{{ $package->price }}" required>
                        <div class="input-group-append">
                            <span class="input-group-text">{{ config('consumer.currency') }}</span>
                        </div>
                    </div>

                    @error('price')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror

                </div>
                <!--/price-->

                <!--operator_price-->
                <div class="form-group">
                    <label for="operator_price"><span class="text-danger">*</span>Operator's Price</label>

                    <div class="input-group">
                        <input name="operator_price" type="number"
                            class="form-control @error('operator_price') is-invalid @enderror" id="operator_price"
                            value="{{ $package->operator_price }}" required>
                        <div class="input-group-append">
                            <span class="input-group-text">{{ config('consumer.currency') }}</span>
                        </div>
                    </div>

                    @error('operator_price')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror

                </div>
                <!--/operator_price-->

                <!--visibility-->
                <div class="form-group">
                    <label for="visibility"><span class="text-danger">*</span>Visibility</label>

                    <div class="input-group">

                        <select class="form-control" id="visibility" name="visibility" required>
                            <option selected>{{ $package->visibility }}</option>
                            <option>public</option>
                            <option>private</option>
                        </select>

                    </div>

                    @error('visibility')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror

                </div>
                <!--/visibility-->

            </div>
            <!--/col-sm-6-->

            <div class="col-sm-6">
                <button type="submit" class="btn btn-dark">Submit</button>
            </div>

        </form>

    </div>

</div>

@endsection

@section('pageJs')
@endsection
