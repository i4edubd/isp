@extends ('laraview.layouts.sideNavLayout')

@section('title')
Edit Billing Profile
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

    <form method="POST"
        action="{{ route('temp_billing_profiles.update', ['temp_billing_profile' => $temp_billing_profile->id]) }}">

        @csrf

        @method('put')

        <div class="card-body">

            <p class="text-danger">* required field</p>

            <div class="row">

                <div class="col-6">

                    <!--minimum_validity-->
                    <div class="form-group">
                        <label for="minimum_validity"><span class="text-danger">*</span>Minimum Validity</label>

                        <div class="input-group">
                            <input name="minimum_validity" type="number" max="30"
                                class="form-control @error('minimum_validity') is-invalid @enderror"
                                id="minimum_validity" value="0" required>
                            <div class="input-group-append">
                                <span class="input-group-text">Days</span>
                            </div>
                        </div>

                        @error('minimum_validity')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                    </div>
                    <!--/minimum_validity-->

                </div>

            </div>

        </div>
        <!--/Card Body-->

        <div class="card-footer">
            <button type="submit" class="btn btn-dark">SUBMIT</button>
        </div>
        <!--/card-footer-->

    </form>

</div>


<div class="card">

    <div class="card-body">
        <dl>
            {{-- Minimum Validity --}}
            <dt><span class="text-danger">Minimum Validity</span></dt>
            <dd>
                <ul>
                    @if (config('consumer.country_code') == 'BD')
                    <li>
                        রিসেলার কাস্টমারকে সর্বনিম্ন কত দিনের জন্য এক্টিভেট করতে পারবেন।
                    </li>
                    <li>
                        Enter 0(Zero) for no restriction.
                    </li>
                    @else
                    <li>
                        The minimum days, the reseller can activate a customer.
                    </li>
                    <li>
                        Enter 0(Zero) for no restriction.
                    </li>
                    @endif
                </ul>
            </dd>
            {{-- Minimum Validity --}}
            <hr>
        </dl>
    </div>
</div>

@endsection

@section('pageJs')
@endsection