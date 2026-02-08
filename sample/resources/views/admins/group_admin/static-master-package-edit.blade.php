@extends ('laraview.layouts.sideNavLayout')

@section('title')
Edit Package
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '6';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection


@section('contentTitle')
<h3> Edit Package </h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <p class="text-danger">* required field</p>

        <form id="quickForm" method="POST"
            action="{{ route('master_packages.update', ['master_package' => $master_package->id]) }}">

            @csrf

            @method('put')

            <div class="col-sm-6">

                <!--name-->
                <div class="form-group">
                    <label for="name"><span class="text-danger">*</span>Name</label>
                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        value="{{ $master_package->name }}" required>

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
                            id="price" value="{{ $master_package->price }}" required>
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