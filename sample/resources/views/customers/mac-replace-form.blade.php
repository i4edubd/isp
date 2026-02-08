@extends ('laraview.layouts.topNavLayout')

@section('title')
    Mobile Verification
@endsection

@section('pageCss')
@endsection

@section('company')
    {{ $operator->company }}
@endsection

@section('topNavbar')
@endsection

@section('contentTitle')
    <h3>Verify it's you</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-header">
            {{ getLocaleString($customer->operator_id, "A verification PIN has been sent to your mobile number (^1 $customer->mobile 1^)", 1) }}
        </div>

        <div class="card-body">
            <div class="col-sm-6">

                <form id="quickForm" method="POST" action="{{ route('customers.replace-mac-address.store') }}"
                    onsubmit="return disableDuplicateSubmit()">
                    @csrf

                    <!--otp-->
                    <div class="form-group">
                        <label for="otp"><span class="text-danger">*</span>
                            {{ getLocaleString($operator->id, 'Verification PIN') }}
                        </label>
                        <input name="otp" type="text" class="form-control" id="otp" inputmode="numeric"
                            autocomplete="one-time-code" required>
                        @error('otp')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <!--/otp-->

                    <button type="submit" id="submit-button" class="btn btn-primary">Submit</button>
                </form>

            </div>
        </div>

    </div>
@endsection

@section('pageJs')
@endsection
