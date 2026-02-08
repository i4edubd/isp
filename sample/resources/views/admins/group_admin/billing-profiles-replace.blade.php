@extends ('laraview.layouts.sideNavLayout')

@section('title')
Billing Profile Replace
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '6';
$active_link = '7';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection


@section('contentTitle')
<h3>Billing Profile Replace</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>

    <form method="POST"
        action="{{ route('billing_profile_replace.update', ['billing_profile' => $billing_profile->id]) }}">

        @csrf

        @method('put')

        <div class="card-body">

            <!--name-->
            <div class="form-group">

                <label for="name">Billing Profile Name (To be Replaced)</label>

                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    value="{{ $billing_profile->name }}" disabled>

                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>
            <!--/name-->

            <!--billing_profile_id-->
            <div class="form-group">
                <label for="billing_profile_id"><span class="text-danger">*</span>Select Billing Profile</label>
                <select class="form-control" id="billing_profile_id" name="billing_profile_id" required>
                    <option value="" selected>please select...</option>
                    @foreach ($profiles->sortBy('name') as $profile)
                    <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                    @endforeach
                </select>
                @error('billing_profile_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <!--/billing_profile_id-->

        </div>
        <!--/Card Body-->

        <div class="card-footer">
            <button type="submit" class="btn btn-dark">Submit</button>
        </div>
        <!--/card-footer-->

    </form>

</div>

@endsection

@section('pageJs')
@endsection
