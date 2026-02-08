@extends ('laraview.layouts.sideNavLayout')

@section('title')
    New SMS Gateway
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '2';
        $active_link = '0';
    @endphp
@endsection

@section('sidebar')
    @include('admins.developer.sidebar')
@endsection


@section('contentTitle')
    <h3>New SMS Gateway</h3>
@endsection

@section('content')
    <div class="card">

        <p class="text-danger">* required field</p>

        <form id="quickForm" method="POST" action="{{ route('sms_gateways.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="card-body">

                <!--operator_id-->
                <div class="form-group">
                    <label for="operator_id"><span class="text-danger">*</span>operator</label>
                    <select class="form-control" id="operator_id" name="operator_id" required>
                        <option value="">Please select... </option>
                        @foreach ($operators as $operator)
                            <option value="{{ $operator->id }}">{{ $operator->id }} :: {{ $operator->role }} ::
                                {{ $operator->company }}</option>
                        @endforeach
                    </select>
                </div>
                <!--/operator_id-->

                <!--saleable-->
                <div class="form-group">
                    <label for="saleable"><span class="text-danger">*</span>saleable(1/0)</label>
                    <input name="saleable" type="number" class="form-control @error('saleable') is-invalid @enderror"
                        id="saleable" required>
                </div>
                <!--saleable-->

                <!--provider_name-->
                <div class="form-group">
                    <label for="provider_name"><span class="text-danger">*</span>Provider Name</label>
                    <select class="form-control" id="provider_name" name="provider_name" required>
                        <option value="maestro">maestro</option>
                        <option value="robi">robi</option>
                        <option value="m2mbd">m2mbd</option>
                        <option value="bangladeshsms">bangladeshsms</option>
                        <option value="bulksmsbd">bulksmsbd</option>
                        <option value="btssms">btssms</option>
                        <option value="880sms">880sms</option>
                        <option value="bdsmartpay">bdsmartpay</option>
                        <option value="elitbuzz">elitbuzz</option>
                        <option value="sslwireless">sslwireless</option>
                        <option value="adnsms">adnsms</option>
                        <option value="24smsbd">24smsbd</option>
                        <option value="smsnet">smsnet</option>
                        <option value="brandsms">brandsms</option>
                        <option value="metrotel">metrotel</option>
                        <option value="dianahost">dianahost</option>
                        <option value="smsinbd">smsinbd</option>
                        <option value="dhakasoftbd">dhakasoftbd</option>
                    </select>
                </div>
                <!--/provider_name-->

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
                    <input name="username" type="text" class="form-control @error('username') is-invalid @enderror"
                        id="username" value="{{ old('username') }}">
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
                    <input name="password" type="text" class="form-control @error('password') is-invalid @enderror"
                        id="password" value="{{ old('password') }}">
                </div>
                <!--/password-->

                <!--from_number-->
                <div class="form-group">
                    <label for="from_number">From Number</label>
                    <input name="from_number" type="text" class="form-control @error('from_number') is-invalid @enderror"
                        id="from_number">
                </div>
                <!--from_number-->

                <!--post_url-->
                <div class="form-group">
                    <label for="post_url">POST URL</label>
                    <input name="post_url" type="text" class="form-control @error('post_url') is-invalid @enderror"
                        id="post_url">
                </div>
                <!--post_url-->

                <!--delivery_report_url-->
                <div class="form-group">
                    <label for="delivery_report_url">Delivery Report URL</label>
                    <input name="delivery_report_url" type="text"
                        class="form-control @error('delivery_report_url') is-invalid @enderror" id="delivery_report_url">
                </div>
                <!--delivery_report_url-->

                <!--balance_check_url-->
                <div class="form-group">
                    <label for="balance_check_url">Balance Check URL</label>
                    <input name="balance_check_url" type="text"
                        class="form-control @error('balance_check_url') is-invalid @enderror" id="balance_check_url">
                </div>
                <!--balance_check_url-->

                <!--unit_price-->
                <div class="form-group">
                    <label for="unit_price">Unit Price</label>
                    <input name="unit_price" type="text"
                        class="form-control @error('unit_price') is-invalid @enderror" id="unit_price">
                </div>
                <!--unit_price-->

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
@endsection
