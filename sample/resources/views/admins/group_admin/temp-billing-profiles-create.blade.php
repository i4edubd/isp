@extends ('laraview.layouts.sideNavLayout')

@section('title')
New Billing Profile
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '5';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
<h3>Create or Update Billing Profiles</h3>
@endsection

@section('content')

<div class="card">

    <form method="POST" action="{{ route('temp_billing_profiles.store') }}">

        @csrf

        <div class="card-body">

            <p class="text-danger">* required field</p>

            <div class="row">

                <div class="col-6">

                    <!--profile_for-->
                    <div class="form-group">

                        <label for="profile_for"><span class="text-danger">*</span>Billing Profiles For</label>

                        <select class="form-control" id="profile_for" name="profile_for" required>
                            <option value="">please sleect...</option>
                            <option value="monthly_billing">Monthly Billing</option>
                            <option value="daily_billing">Jump Billing</option>
                            <option value="free_customer">Free Customer</option>
                        </select>

                        @error('profile_for')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                    </div>
                    <!--/profile_for-->

                </div>

            </div>

        </div>
        <!--/Card Body-->

        <div class="card-footer">
            <button type="submit" class="btn btn-dark">NEXT <i class="fas fa-arrow-right"></i></button>
        </div>
        <!--/card-footer-->

    </form>

</div>

@endsection

@section('pageJs')
@endsection
