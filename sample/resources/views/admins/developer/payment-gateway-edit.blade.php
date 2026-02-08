@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Payment Gateway Edit
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
    <h3>Edit Payment Gateway</h3>
@endsection

@section('content')
    <div class="card">

        <p class="text-danger">* required field</p>

        <form id="quickForm" method="POST"
            action="{{ route('payment_gateways.update', ['payment_gateway' => $payment_gateway->id]) }}"
            enctype="multipart/form-data">

            @csrf

            @method('put')

            <div class="card-body">

                <div class="row">

                    <div class="col-sm">

                        <!--operator_id-->
                        <div class="form-group">
                            <label for="operator_id">operator</label>
                            <input class="form-control" id="operator_id" type="text"
                                placeholder="{{ $payment_gateway->operator->company }} {{ $payment_gateway->operator->role }}"
                                readonly>
                        </div>
                        <!--/operator_id-->

                        <!--country_code-->
                        <div class="form-group">
                            <label for="country_code"><span class="text-danger">*</span>Country Code</label>
                            <input class="form-control" id="country_code" type="text"
                                placeholder="{{ $payment_gateway->country_code }}" readonly>
                        </div>
                        <!--/country_code-->

                        <!--provider_name-->
                        <div class="form-group">
                            <label for="provider_name">Provider Name</label>
                            <input class="form-control" id="provider_name" type="text"
                                placeholder="{{ $payment_gateway->provider_name }}" readonly>
                        </div>
                        <!--/provider_name-->

                        <!--send_money_provider-->
                        <div class="form-group">
                            <label for="send_money_provider">Send Money Provider</label>
                            <input class="form-control" id="send_money_provider" type="text"
                                placeholder="{{ $payment_gateway->send_money_provider }}" readonly>
                        </div>
                        <!--/send_money_provider-->

                        <!--payment_method-->
                        <div class="form-group">
                            <label for="payment_method"><span class="text-danger">*</span>Payment Method</label>
                            <input name="payment_method" type="text"
                                class="form-control @error('payment_method') is-invalid @enderror" id="payment_method"
                                value="{{ $payment_gateway->payment_method }}" required>
                        </div>
                        <!--/payment_method-->

                        <!--token-->
                        <div class="form-group">
                            <label for="token">Token</label>
                            <input name="token" type="text" class="form-control @error('token') is-invalid @enderror"
                                id="token" value="{{ $payment_gateway->token }}">
                        </div>
                        <!--/token-->

                        <!--username-->
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input name="username" type="text"
                                class="form-control @error('username') is-invalid @enderror" id="username"
                                value="{{ $payment_gateway->username }}">
                        </div>
                        <!--/username-->

                        <!--password-->
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input name="password" type="text"
                                class="form-control @error('password') is-invalid @enderror" id="password"
                                value="{{ $payment_gateway->password }}">
                        </div>
                        <!--/password-->

                        <!--email-->
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input name="email" type="text" class="form-control @error('email') is-invalid @enderror"
                                id="email" value="{{ $payment_gateway->email }}">
                        </div>
                        <!--/email-->

                    </div>
                    <!--/Left Column-->
                    <div class="col-sm">

                        <!--app_key-->
                        <div class="form-group">
                            <label for="app_key">app_key</label>
                            <input name="app_key" type="text" class="form-control @error('app_key') is-invalid @enderror"
                                id="app_key" value="{{ $payment_gateway->app_key }}">
                        </div>
                        <!--app_key-->

                        <!--app_secret-->
                        <div class="form-group">
                            <label for="app_secret">app_secret</label>
                            <input name="app_secret" type="text" class="form-control @error('app_secret') is-invalid @enderror"
                                id="app_secret" value="{{ $payment_gateway->app_secret }}">
                        </div>
                        <!--app_secret-->

                        <!--private_key-->
                        <div class="form-group">
                            <label for="private_key">private_key</label>
                            <input name="private_key" type="text" class="form-control @error('private_key') is-invalid @enderror"
                                id="private_key" value="{{ $payment_gateway->private_key }}">
                        </div>
                        <!--private_key-->

                        <!--public_key-->
                        <div class="form-group">
                            <label for="public_key">public_key</label>
                            <input name="public_key" type="text" class="form-control @error('public_key') is-invalid @enderror"
                                id="public_key" value="{{ $payment_gateway->public_key }}">
                        </div>
                        <!--public_key-->

                        <!--msisdn-->
                        <div class="form-group">
                            <label for="msisdn">msisdn</label>
                            <input name="msisdn" type="text" class="form-control @error('msisdn') is-invalid @enderror"
                                id="msisdn" value="{{ $payment_gateway->msisdn }}">
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
                                value="{{ $payment_gateway->inheritable }}" required>
                        </div>
                        <!--inheritable-->

                        <!--service_charge_percentage-->
                        <div class="form-group">
                            <label for="service_charge_percentage">Service Charge Percentage</label>
                            <input name="service_charge_percentage" type="text"
                                class="form-control @error('service_charge_percentage') is-invalid @enderror"
                                id="service_charge_percentage" value="{{ $payment_gateway->service_charge_percentage }}">
                        </div>
                        <!--service_charge_percentage-->

                    </div>
                    <!--/Right Column-->

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
