@section('content')

<div class="card">

    <div class="card-body">

        <div class="row">

            <div class="col-sm-6">

                <form method="POST"
                    action="{{ route('temp_customer.tech_info.store', ['temp_customer' => $temp_customer->id]) }}">

                    @csrf

                    @if ($temp_customer->connection_type == 'PPPoE')
                    <!--username-->
                    <div class="form-group">
                        <label for="username"><span class="text-danger">*</span>username</label>
                        <input name="username" type="text" class="form-control @error('username') is-invalid @enderror"
                            id="username" value="{{ old('username') }}" autocomplete="off"
                            onblur="checkDuplicateUsername(this.value)" required>
                        @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                        <div id="duplicate_username_response"></div>

                    </div>
                    <!--/username-->

                    <!--password-->
                    <div class="form-group">
                        <label for="password"><span class="text-danger">*</span>password</label>
                        <input name="password" type="text" class="form-control @error('password') is-invalid @enderror"
                            id="password" value="{{ old('password') }}" required>
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/password-->
                    @endif

                    <!--package_id-->
                    <div class="form-group">
                        <label for="package_id"><span class="text-danger">*</span>Package</label>
                        <select class="form-control" id="package_id" name="package_id" required>
                            <option value="">Please select...</option>
                            @foreach ($packages->sortBy('name') as $package)
                            <option value="{{ $package->id }}">{{ $package->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!--/package_id-->

                    @if ($temp_customer->connection_type == 'PPPoE' && $temp_customer->billing_type == 'Daily')
                    <!--validity-->
                    <div class="form-group">

                        <label for="validity"><span class="text-danger">*</span>Validity</label>

                        <div class="input-group">
                            <input name="validity" type="number" min="{{ $billing_profile->minimum_validity }}"
                                class="form-control @error('validity') is-invalid @enderror" id="validity"
                                value="{{ $billing_profile->minimum_validity }}" aria-describedby="validityHelpBlock"
                                required>
                            <div class="input-group-append">
                                <span class="input-group-text">Days</span>
                            </div>
                        </div>

                        <small id="validityHelpBlock" class="form-text text-muted">
                            Minimum Required Validity : {{ $billing_profile->minimum_validity }} Days
                        </small>

                        @error('validity')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                    </div>
                    <!--/validity-->
                    @endif

                    @if ($temp_customer->connection_type == 'StaticIp')
                    <!--router_id-->
                    <div class="form-group">
                        <label for="router_id"><span class="text-danger">*</span>Router</label>

                        <select class="form-control" id="router_id" name="router_id" required>

                            @foreach ($routers as $router)
                            <option value="{{ $router->id }}">{{ $router->location }} :: {{ $router->nasname }}
                            </option>
                            @endforeach

                        </select>

                        @error('router_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/router_id-->

                    <!--login_ip-->
                    <div class="form-group">
                        <label for="login_ip"><span class="text-danger">*</span>IP Address</label>
                        <input name="login_ip" type="text" class="form-control @error('login_ip') is-invalid @enderror"
                            id="login_ip" value="{{ old('login_ip') }}" required>

                        @error('login_ip')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                    </div>
                    <!--/login_ip-->
                    @endif

                    @if ($temp_customer->connection_type == 'PPPoE')
                    {{-- sms_password --}}
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="yes" id="defaultCheck1"
                            name="sms_password" checked>
                        <label class="form-check-label" for="defaultCheck1">
                            Send username and password to customer's mobile.
                        </label>
                    </div>
                    {{-- sms_password --}}
                    @endif

                    <button type="submit" class="btn btn-dark">NEXT<i class="fas fa-arrow-right"></i></button>

                </form>

            </div>
            <!--/col-sm-6-->

        </div>
        <!--/row-->

    </div>
    <!--/card-body-->

</div>

@endsection

@section('pageJs')

<script>
    function checkDuplicateUsername(username)
    {
        let url = "/admin/check-customers-uniqueness?attribute=username&value=" + username;
        $.get( url, function( data ) {
            $("#duplicate_username_response").html(data);
        });
    }
</script>

@endsection