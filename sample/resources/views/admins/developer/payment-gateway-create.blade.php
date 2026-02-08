@extends ('laraview.layouts.sideNavLayout')

@section('title')
    New Payment Gateway
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '1';
        $active_link = '0';
    @endphp
@endsection

@section('sidebar')
    @include('admins.developer.sidebar')
@endsection

@section('contentTitle')
    <h3>New Payment Gateway</h3>
@endsection

@section('content')
    <div class="card">

        <p class="text-danger">* required field</p>

        <form id="quickForm" method="POST" action="{{ route('payment_gateways.store') }}" enctype="multipart/form-data">

            @csrf

            <div class="card-body">

                <div class="row">

                    <div class="col-sm">

                        <!--operator_id-->
                        <div class="form-group">
                            <label for="operator_id"><span class="text-danger">*</span>operator</label>
                            <select class="form-control" id="operator_id" name="operator_id" required>
                                <option value="">Please select... </option>
                                @foreach ($operators as $operator)
                                    <option value="{{ $operator->id }}">
                                        {{ $operator->id }}, {{ $operator->company }}, {{ $operator->role }},
                                        {{ $operator->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!--/operator_id-->

                        {{-- country_id --}}
                        <div class="form-group">
                            <label for="country_id">Country</label>
                            <select class="form-control" id="country_id" name="country_id" required>
                                <option value="">select...</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- country_id --}}

                        <!--provider_name-->
                        <div class="form-group">
                            <label for="provider_name"><span class="text-danger">*</span>Provider Name</label>
                            <select class="form-control" id="provider_name" name="provider_name" required>
                                <option value="bkash_checkout">bkash_checkout</option>
                                <option value="bkash_tokenized_checkout">bkash_tokenized_checkout</option>
                                <option value="easypayway">easypayway</option>
                                <option value="sslcommerz">sslcommerz</option>
                                <option value="walletmix">walletmix</option>
                                <option value="bdsmartpay">bdsmartpay</option>
                                <option value="aamarpay">aamarpay</option>
                                <option value="nagad">nagad</option>
                                <option value="rocket">rocket</option>
                                <option value="recharge_card">recharge_card</option>
                                <option value="send_money">Send Money</option>
                                <option value="bkash_payment">bKash Payment</option>
                                <option value="shurjopay">shurjoPay</option>
                                <option value="razorpay">razorpay</option>
                            </select>
                        </div>
                        <!--/provider_name-->

                        <!--send_money_provider-->
                        <div class="form-group">
                            <label for="send_money_provider">Send Money Provider</label>
                            <select class="form-control" id="send_money_provider" name="send_money_provider">
                                <option value="">select...</option>
                                <option value="bkash">bkash</option>
                                <option value="nagad">nagad</option>
                                <option value="rocket">rocket</option>
                            </select>
                        </div>
                        <!--/send_money_provider-->

                        <!--payment_method-->
                        <div class="form-group">
                            <label for="payment_method"><span class="text-danger">*</span>Payment Method</label>
                            <input name="payment_method" type="text"
                                class="form-control @error('payment_method') is-invalid @enderror" id="payment_method"
                                value="{{ old('payment_method') }}" required>
                        </div>
                        <!--/payment_method-->

                        <!--token-->
                        <div class="form-group">
                            <label for="token">Token</label>
                            <input name="token" type="text" class="form-control @error('token') is-invalid @enderror"
                                id="token" value="{{ old('token') }}">
                        </div>
                        <!--/token-->

                        <!--username-->
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input name="username" type="text"
                                class="form-control @error('username') is-invalid @enderror" id="username"
                                value="{{ old('username') }}">
                        </div>
                        <!--/username-->

                        <!--email-->
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input name="email" type="text" class="form-control @error('email') is-invalid @enderror"
                                id="email" value="{{ old('email') }}">
                        </div>
                        <!--/email-->

                        <!--password-->
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input name="password" type="text"
                                class="form-control @error('password') is-invalid @enderror" id="password"
                                value="{{ old('password') }}">
                        </div>
                        <!--/password-->

                    </div>
                    <!--/Left Column-->
                    <div class="col-sm">

                        <!--app_key-->
                        <div class="form-group">
                            <label for="app_key">app_key</label>
                            <input name="app_key" type="text" class="form-control @error('app_key') is-invalid @enderror"
                                id="app_key">
                        </div>
                        <!--app_key-->

                        <!--app_secret-->
                        <div class="form-group">
                            <label for="app_secret">app_secret</label>
                            <input name="app_secret" type="text"
                                class="form-control @error('app_secret') is-invalid @enderror" id="app_secret">
                        </div>
                        <!--app_secret-->

                        <!--private_key-->
                        <div class="form-group">
                            <label for="private_key">private_key</label>
                            <input name="private_key" type="text"
                                class="form-control @error('private_key') is-invalid @enderror" id="private_key">
                        </div>
                        <!--private_key-->

                        <!--public_key-->
                        <div class="form-group">
                            <label for="public_key">public_key</label>
                            <input name="public_key" type="text"
                                class="form-control @error('public_key') is-invalid @enderror" id="public_key">
                        </div>
                        <!--public_key-->

                        <!--msisdn-->
                        <div class="form-group">
                            <label for="msisdn">msisdn</label>
                            <input name="msisdn" type="text"
                                class="form-control @error('msisdn') is-invalid @enderror" id="msisdn">
                        </div>
                        <!--msisdn-->

                        <!--credentials_path-->
                        <div class="form-group row">
                            <label for="credentials_path">Credentials: </label>
                            <div class="custom-file">
                                <input type="file" name="credentials_path" class="custom-file-input"
                                    id="credentials_path">
                                <label class="custom-file-label" for="credentials_path">Choose file</label>
                            </div>
                        </div>
                        <!--/credentials_path-->

                        <!--inheritable-->
                        <div class="form-group">
                            <label for="inheritable"><span class="text-danger">*</span>Inheritable(1/0)</label>
                            <input name="inheritable" type="text"
                                class="form-control @error('inheritable') is-invalid @enderror" id="inheritable"
                                required>
                        </div>
                        <!--inheritable-->

                        <!--service_charge_percentage-->
                        <div class="form-group">
                            <label for="service_charge_percentage">Service Charge Percentage</label>
                            <input name="service_charge_percentage" type="text"
                                class="form-control @error('service_charge_percentage') is-invalid @enderror"
                                id="service_charge_percentage">
                        </div>
                        <!--service_charge_percentage-->

                    </div>
                    <!--/Right Column--->

                </div>
                <!--/row-->

            </div>
            <!--/card-body-->

            <div class="card-footer">
                <button type="submit" class="btn btn-dark">Submit</button>
            </div>
            <!--/card-footer-->

        </form>

    </div>
@endsection

@section('pageJs')
    <script type="text/javascript">
        $(document).ready(function() {
            bsCustomFileInput.init();
        });
    </script>
@endsection
