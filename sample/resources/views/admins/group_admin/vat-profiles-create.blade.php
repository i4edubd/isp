@extends ('laraview.layouts.sideNavLayout')

@section('title')
New VAT Profile
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '40';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection


@section('contentTitle')
<h3>New VAT Profile</h3>
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">VAT</li>
    <li class="breadcrumb-item active">New Profile</li>
</ol>
@endsection

@section('content')

<div class="row">

    <div class="col-sm-6">

        <div class="card">

            <p class="text-danger">* required field</p>

            <form method="POST" action="{{ route('vat_profiles.store') }}">

                @csrf

                <div class="card-body">

                    <!--description-->
                    <div class="form-group">
                        <label for="description"><span class="text-danger">*</span>Description</label>
                        <input name="description" type="text"
                            class="form-control @error('description') is-invalid @enderror" id="description"
                            value="{{ old('description') }}" required>
                        @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/description-->

                    <!--identification_number-->
                    <div class="form-group">
                        <label for="identification_number"><span class="text-danger">*</span>VAT Identification
                            Number</label>
                        <input name="identification_number" type="text"
                            class="form-control @error('identification_number') is-invalid @enderror"
                            id="identification_number" value="{{ old('identification_number') }}" required>
                        @error('identification_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/identification_number-->

                    <!--rate-->
                    <div class="form-group">
                        <label for="rate"><span class="text-danger">*</span>VAT Rate</label>

                        <div class="input-group">
                            <input name="rate" type="number" class="form-control @error('rate') is-invalid @enderror"
                                id="rate" value="{{ old('rate') }}" required>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    %
                                </span>
                            </div>
                        </div>

                        @error('rate')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                    </div>
                    <!--/rate-->

                    <!--status-->
                    <div class="form-group">
                        <label for="status"><span class="text-danger">*</span>Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="enabled">Enabled</option>
                            <option value="disabled">Disabled</option>
                        </select>
                        @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/status-->

                </div>
                <!--/Card Body-->

                <div class="card-footer">
                    <button type="submit" class="btn btn-dark">Submit</button>
                </div>
                <!--/card-footer-->

            </form>

        </div>

    </div>

</div>

@endsection

@section('pageJs')
@endsection