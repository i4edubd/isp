@section('contentTitle')
    <h3> Edit customer </h3>
@endsection

@section('content')

    <div class="card">

        <p class="text-danger">* required field</p>

        <form id="quickForm" method="POST"
            action="{{ route('customers.update', ['customer' => $customer->id, 'page' => $page]) }}">

            <div class="card-body">

                <div class="row">

                    <div class="col-sm-6">

                        @csrf

                        @method('put')

                        <!--zone_id-->
                        <div class="form-group">
                            <label for="zone_id">Customer Zone</label>
                            <select class="form-control" id="zone_id" name="zone_id" @required(isMandatoryCustomerAttribute('zone_id', Auth::user()))>
                                <option value="{{ $customer->zone_id }}" selected>{{ $customer->zone }}</option>
                                @foreach ($customer_zones as $customer_zone)
                                    <option value="{{ $customer_zone->id }}">{{ $customer_zone->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--/zone_id-->

                        <!--device_id-->
                        <div class="form-group">
                            <label for="device_id">Device</label>
                            <select class="form-control" id="device_id" name="device_id">
                                <option value="{{ $customer->device_id }}" selected>{{ $customer->device }}</option>
                                @foreach ($devices as $device)
                                    <option value="{{ $device->id }}">{{ $device->name }} ({{ $device->location }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!--/device_id-->

                        <!--name-->
                        <div class="form-group">
                            <label for="name"><span class="text-danger">*</span>Name</label>
                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" value="{{ $customer->name }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/name-->

                        <!--mobile-->
                        <div class="form-group">
                            <label for="mobile"><span class="text-danger">*</span>Mobile</label>
                            <input name="mobile" type="text" class="form-control @error('mobile') is-invalid @enderror"
                                id="mobile" value="{{ $customer->mobile }}" onblur="checkDuplicateMobile(this.value)"
                                required>
                            @error('mobile')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <div id="duplicate_mobile_response"></div>

                        </div>
                        <!--/mobile-->

                        <!--email-->
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input name="email" type="text" class="form-control @error('email') is-invalid @enderror"
                                id="email" value="{{ $customer->email }}" @required(isMandatoryCustomerAttribute('email', Auth::user()))>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/email-->

                        <!--nid-->
                        <div class="form-group">
                            <label for="nid">NID Number</label>
                            <input name="nid" type="text" class="form-control @error('nid') is-invalid @enderror"
                                id="nid" value="{{ $customer->nid }}" autocomplete="nid" @required(isMandatoryCustomerAttribute('nid', Auth::user()))>
                            @error('nid')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/nid-->

                        <!--comment-->
                        <div class="form-group">
                            <label for="comment">Comment</label>
                            <input name="comment" type="text" class="form-control @error('comment') is-invalid @enderror"
                                id="comment" value="{{ $customer->comment }}" autocomplete="comment">
                            @error('comment')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/comment-->

                    </div>
                    <!--/col-sm-6-->

                    <div class="col-sm-6">

                        <!--username-->
                        <div class="form-group">
                            <label for="username"><span class="text-danger">*</span>username</label>
                            <input name="username" type="text"
                                class="form-control @error('username') is-invalid @enderror" id="username"
                                value="{{ $customer->username }}" autocomplete="username"
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
                            <input name="password" type="text"
                                class="form-control @error('password') is-invalid @enderror" id="password"
                                value="{{ $customer->password }}" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/password-->

                        <!--login_mac_address-->
                        <div class="form-group">
                            <label for="login_mac_address">MAC Address</label>
                            <input name="login_mac_address" type="text"
                                class="form-control @error('login_mac_address') is-invalid @enderror"
                                id="login_mac_address" value="{{ $customer->login_mac_address }}">
                            @error('login_mac_address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/login_mac_address-->

                        <!--house_no-->
                        <div class="form-group">
                            <label for="house_no">House#</label>
                            <input name="house_no" type="text"
                                class="form-control @error('house_no') is-invalid @enderror" id="house_no"
                                value="{{ $customer->house_no }}" @required(isMandatoryCustomerAttribute('house_no', Auth::user()))>
                            @error('house_no')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/house_no-->

                        <!--road_no-->
                        <div class="form-group">
                            <label for="road_no">Road#</label>
                            <input name="road_no" type="text"
                                class="form-control @error('road_no') is-invalid @enderror" id="road_no"
                                value="{{ $customer->road_no }}" @required(isMandatoryCustomerAttribute('road_no', Auth::user()))>
                            @error('road_no')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/road_no-->

                        <!--thana-->
                        <div class="form-group">
                            <label for="thana">Thana</label>
                            <input name="thana" type="text"
                                class="form-control @error('thana') is-invalid @enderror" id="thana"
                                value="{{ $customer->thana }}" @required(isMandatoryCustomerAttribute('thana', Auth::user()))>
                            @error('thana')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/thana-->

                        <!--district-->
                        <div class="form-group">
                            <label for="district">District</label>
                            <input name="district" type="text"
                                class="form-control @error('district') is-invalid @enderror" id="district"
                                value="{{ $customer->district }}" @required(isMandatoryCustomerAttribute('district', Auth::user()))>
                            @error('district')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/district-->

                    </div>
                    <!--/col-sm-6-->

                </div>
                <!--/row-->

                {{-- --}}
                @if ($customer->connection_type === 'StaticIp')
                    <div class="row">
                        <div class="col-sm-6">
                            <!--router_id-->
                            <div class="form-group">
                                <label for="router_id"><span class="text-danger">*</span>Router</label>

                                <select class="form-control" id="router_id" name="router_id" required>

                                    <option value="{{ $customer->router_id }}" selected>{{ $customer->router->nasname }}
                                    </option>

                                    @foreach ($routers as $router)
                                        <option value="{{ $router->id }}">{{ $router->location }} ::
                                            {{ $router->nasname }}
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
                        </div>
                        <div class="col-sm-6">
                            <!--login_ip-->
                            <div class="form-group">
                                <label for="login_ip"><span class="text-danger">*</span>IP Address</label>
                                <input name="login_ip" type="text"
                                    class="form-control @error('login_ip') is-invalid @enderror" id="login_ip"
                                    value="{{ $customer->login_ip }}" required>

                                @error('login_ip')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>
                            <!--/login_ip-->
                        </div>
                    </div>
                @endif
                {{-- --}}

                {{-- custom fields --}}
                <div class="row">
                    @foreach ($custom_fields as $custom_field)
                        <div class="col">
                            <div class="form-group">
                                <label for="{{ $custom_field->id }}">{{ $custom_field->name }}</label>
                                <input name="{{ $custom_field->id }}" type="text" class="form-control"
                                    id="{{ $custom_field->id }}" value="{{ $custom_field->value }}">
                            </div>
                        </div>
                    @endforeach
                </div>
                {{-- custom fields --}}

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
    <script>
        function checkDuplicateMobile(mobile) {
            let url = "/admin/check-customers-uniqueness?attribute=mobile&value=" + mobile;
            $.get(url, function(data) {
                $("#duplicate_mobile_response").html(data);
            });
        }

        function checkDuplicateUsername(username) {
            let url = "/admin/check-customers-uniqueness?attribute=username&value=" + username;
            $.get(url, function(data) {
                $("#duplicate_username_response").html(data);
            });
        }
    </script>
@endsection
