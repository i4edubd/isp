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
    <h3>Edit Billing Profile</h3>
@endsection

@section('content')
    <div class="card">

        <form method="POST" action="{{ route('billing_profiles.update', ['billing_profile' => $billing_profile->id]) }}">

            @csrf

            @method('put')

            <div class="card-body">

                <p class="text-danger">* required field</p>

                <div class="row">

                    <div class="col-6">

                        <!--billing_type-->
                        <div class='form-group'>
                            <label for='billing_type'>Billing Type</label>
                            <input type='text' id='billing_type' class='form-control'
                                value="{{ $billing_profile->billing_type }}" disabled>
                        </div>
                        <!--/billing_type-->

                        <!--profile_name-->
                        <div class='form-group'>
                            <label for='profile_name'><span class="text-danger">*</span>Profile Name</label>
                            <input type='text' name="profile_name" id='profile_name' class='form-control'
                                value="{{ $billing_profile->profile_name }}" required>
                        </div>
                        <!--/profile_name-->

                        <!--minimum_validity-->
                        @if ($billing_profile->billing_type == 'Daily')
                            <div class="form-group">
                                <label for="minimum_validity"><span class="text-danger">*</span>Minimum Validity</label>

                                <div class="input-group">
                                    <input name="minimum_validity" type="number"
                                        class="form-control @error('minimum_validity') is-invalid @enderror"
                                        id="minimum_validity" value="{{ $billing_profile->minimum_validity }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            Day(s)
                                        </span>
                                    </div>
                                </div>

                                @error('minimum_validity')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>
                        @endif
                        <!--/minimum_validity-->

                        <!--billing_due_date-->
                        @if ($billing_profile->billing_type == 'Monthly')
                            @can('updatePaymentDate', $billing_profile)
                                <div class='form-group'>
                                    <label for='datepicker'><span class="text-danger">*</span>Last Date of Payment</label>
                                    <input type='text' name="billing_due_date" id='datepicker' class='form-control'
                                        value="{{ $billing_profile->payment_date }}" required>
                                </div>
                            @else
                                <div class='form-group'>
                                    <label for='billing_due_date'>Last Date of Payment</label>
                                    <input type='text' id='billing_due_date' class='form-control'
                                        value="{{ $billing_profile->payment_date }}" disabled>
                                </div>
                            @endcan
                        @endif
                        <!--/billing_due_date-->

                        <!--auto_bill-->
                        @if ($billing_profile->billing_type == 'Monthly')
                            <div class="form-group">
                                <label for="auto_bill"><span class="text-danger">*</span>Automatic bill?</label>
                                <select class="form-control" id="auto_bill" name="auto_bill" required>
                                    <option selected>{{ $billing_profile->auto_bill }}</option>
                                    <option>yes</option>
                                    <option>no</option>
                                </select>
                                @error('auto_bill')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        @endif
                        <!--/auto_bill-->

                        <!--auto_lock-->
                        @if ($billing_profile->billing_type == 'Monthly')
                            <div class="form-group">
                                <label for="auto_lock"><span class="text-danger">*</span>Automatic Suspend?</label>
                                <select class="form-control" id="auto_lock" name="auto_lock" required>
                                    <option selected>{{ $billing_profile->auto_lock }}</option>
                                    <option>yes</option>
                                    <option>no</option>
                                </select>
                                @error('auto_lock')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        @endif
                        <!--/auto_lock-->

                    </div>

                </div>

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
    <script>
        $(function() {
            $('#datepicker').datepicker({
                autoclose: !0
            });
        });
    </script>
@endsection
