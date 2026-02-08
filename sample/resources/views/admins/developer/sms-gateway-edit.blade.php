@extends ('laraview.layouts.sideNavLayout')

@section('title')
    SMS Gateway Edit
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
    <h3>Edit SMS Gateway</h3>
@endsection

@section('content')
    <div class="card">

        <p class="text-danger">* required field</p>

        <form id="quickForm" method="POST" action="{{ route('sms_gateways.update', ['sms_gateway' => $sms_gateway->id]) }}"
            enctype="multipart/form-data">
            @csrf
            @method('put')

            <div class="card-body">

                <!--operator_id-->
                <div class="form-group">
                    <label for="operator_id"><span class="text-danger">*</span>operator</label>
                    <input class="form-control" id="operator_id" type="text"
                        placeholder="{{ $sms_gateway->operator->company }} {{ $sms_gateway->operator->role }}" readonly>
                </div>
                <!--/operator_id-->

                <!--saleable-->
                <div class="form-group">
                    <label for="saleable"><span class="text-danger">*</span>saleable(1/0)</label>
                    <input name="saleable" type="number" value="{{ $sms_gateway->saleable }}"
                        class="form-control @error('saleable') is-invalid @enderror" required>
                </div>
                <!--saleable-->

                <!--provider_name-->
                <div class="form-group">
                    <label for="provider_name"><span class="text-danger">*</span>Provider Name</label>
                    <input class="form-control" id="provider_name" type="text"
                        placeholder="{{ $sms_gateway->provider_name }}" readonly>
                </div>
                <!--/provider_name-->

                <!--country_code-->
                <div class="form-group">
                    <label for="country_code"><span class="text-danger">*</span>Country Code</label>
                    <input class="form-control" id="country_code" type="text"
                        placeholder="{{ $sms_gateway->country_code }}" readonly>
                </div>
                <!--/country_code-->

                <!--token-->
                <div class="form-group">
                    <label for="token">Token</label>
                    <input name="token" type="text" class="form-control @error('token') is-invalid @enderror"
                        id="token" value="{{ $sms_gateway->token }}">
                </div>
                <!--/token-->

                <!--username-->
                <div class="form-group">
                    <label for="username">Username</label>
                    <input name="username" type="text" class="form-control @error('username') is-invalid @enderror"
                        id="username" value="{{ $sms_gateway->username }}">
                </div>
                <!--/username-->

                <!--password-->
                <div class="form-group">
                    <label for="password">Password</label>
                    <input name="password" type="text" class="form-control @error('password') is-invalid @enderror"
                        id="password" value="{{ $sms_gateway->password }}">
                </div>
                <!--/password-->

                <!--email-->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input name="email" type="text" class="form-control @error('email') is-invalid @enderror"
                        id="email" value="{{ $sms_gateway->email }}">
                </div>
                <!--/email-->

                <!--from_number-->
                <div class="form-group">
                    <label for="from_number">From Number</label>
                    <input name="from_number" type="text" class="form-control @error('from_number') is-invalid @enderror"
                        id="from_number" value="{{ $sms_gateway->from_number }}">
                </div>
                <!--from_number-->

                <!--post_url-->
                <div class="form-group">
                    <label for="post_url">POST URL</label>
                    <input name="post_url" type="text" class="form-control @error('post_url') is-invalid @enderror"
                        id="post_url" value="{{ $sms_gateway->post_url }}">
                </div>
                <!--post_url-->

                <!--delivery_report_url-->
                <div class="form-group">
                    <label for="delivery_report_url">Delivery Report URL</label>
                    <input name="delivery_report_url" type="text"
                        class="form-control @error('delivery_report_url') is-invalid @enderror" id="delivery_report_url"
                        value="{{ $sms_gateway->delivery_report_url }}">
                </div>
                <!--delivery_report_url-->

                <!--balance_check_url-->
                <div class="form-group">
                    <label for="balance_check_url">Balance Check URL</label>
                    <input name="balance_check_url" type="text"
                        class="form-control @error('balance_check_url') is-invalid @enderror" id="balance_check_url"
                        value="{{ $sms_gateway->balance_check_url }}">
                </div>
                <!--balance_check_url-->

                <!--unit_price-->
                <div class="form-group">
                    <label for="unit_price">Unit Price</label>
                    <input name="unit_price" type="text" class="form-control @error('unit_price') is-invalid @enderror"
                        id="unit_price" value="{{ $sms_gateway->unit_price }}">
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
