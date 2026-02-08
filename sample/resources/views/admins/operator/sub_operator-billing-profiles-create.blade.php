@extends ('laraview.layouts.sideNavLayout')

@section('title')
Assign Billing Profiles
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '10';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@section('contentTitle')
<h3> Assign Billing Profiles</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <div class="col-sm-6">

            <form id="quickForm" method="POST"
                action="{{ route('sub_operators.billing_profiles.store', ['operator' => $operator->id ]) }}">

                @csrf

                {{-- operator --}}
                <div class="form-group">
                    <label for="disabledTextInput">Reseller</label>
                    <input type="text" id="disabledTextInput" class="form-control" placeholder="{{ $operator->name }}"
                        disabled>
                </div>
                {{-- operator --}}

                <div class="card-header font-weight-bold">Select Profiles</div>

                {{-- assigned_profiles --}}
                @foreach ($assigned_profiles as $assigned_profile)
                <div class="form-check">
                    <input name="billing_profile_ids[]" class="form-check-input" type="checkbox"
                        value="{{ $assigned_profile->id }}" id="{{ $assigned_profile->id }}" checked>
                    <label class="form-check-label" for="{{ $assigned_profile->id }}">
                        {{ $assigned_profile->name }}
                    </label>
                </div>
                @endforeach
                {{-- assigned_profiles --}}

                {{-- profiles --}}
                @foreach ($profiles as $profile)
                <div class="form-check">
                    <input name="billing_profile_ids[]" class="form-check-input" type="checkbox"
                        value="{{ $profile->id }}" id="{{ $profile->id }}">
                    <label class="form-check-label" for="{{ $profile->id }}">
                        {{ $profile->name }}
                    </label>
                </div>
                @endforeach
                {{-- profiles --}}

                <button type="submit" class="btn btn-primary mt-2">Submit</button>

            </form>

        </div>

    </div>

</div>

@endsection

@section('pageJs')
@endsection
