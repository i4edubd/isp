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

                    <!--cycle_ends_with_month-->
                    <div class="form-group">

                        <label for="cycle_ends_with_month"><span class="text-danger">*</span>
                            Billing cycle ends with month?
                        </label>

                        <select class="form-control" id="cycle_ends_with_month" name="cycle_ends_with_month" required>
                            <option selected>no</option>
                            <option>yes</option>
                        </select>

                        @error('cycle_ends_with_month')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                    </div>
                    <!--/cycle_ends_with_month-->

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
            {{-- Case 1 --}}
            <dt><span class="text-danger"> Billing cycle ends with month = no </span></dt>
            <dd>
                <ul>
                    <li>
                        আপনি কি নতুন কাস্টমারকে পুরো এক মাসের জন্য বিল করেন? তাহলে Billing cycle ends with month = no
                        সিলেক্ট করুন।
                    </li>
                </ul>
            </dd>
            {{-- Case 1 --}}
            <hr>
            {{-- Case 2 --}}
            <dt><span class="text-danger"> Billing cycle ends with month = yes </span></dt>
            <dd>
                <ul>
                    <li>
                        আপনি কি নতুন কাস্টমারকে মাসের যে কয়দিন বাকি আছে সেই কয়দিনের জন্য বিল করেন? তাহলে Billing cycle
                        ends
                        with month = yes সিলেক্ট করুন।
                    </li>
                    <li>
                        ধরুন একজন কাস্টমার মাসের ১৫ তারিখে কানেকশন নিল। তাহলে তাকে এক্ষেত্রে ১৫ দিনের জন্য বিল করা হবে।
                    </li>
                </ul>
            </dd>
            {{-- Case 2 --}}
        </dl>
    </div>
</div>

@endsection

@section('pageJs')
@endsection