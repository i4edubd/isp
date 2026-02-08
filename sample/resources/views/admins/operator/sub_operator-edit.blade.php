@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Edit Reseller
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '10';
        $active_link = '0';
    @endphp
@endsection

@section('sidebar')
    @include('admins.operator.sidebar')
@endsection

@section('contentTitle')
    <h3>Edit Reseller</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <p class="text-danger">* required field</p>

            <div class="row">
                <div class="col-sm-6">
                    <form id="quickForm" method="POST"
                        action="{{ route('sub_operators.update', ['sub_operator' => $sub_operator->id]) }}">
                        @csrf
                        @method('put')

                        <!--company-->
                        <div class="form-group">
                            <label for="company"><span class="text-danger">*</span>Company</label>
                            <input name="company" type="text" class="form-control @error('company') is-invalid @enderror"
                                id="company" value="{{ $sub_operator->company }}" autocomplete="company" required>
                            @error('company')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/company-->

                        <!--company_in_native_lang-->
                        <div class="form-group">
                            <label for="company_in_native_lang"><span class="text-danger">*</span>
                                Company Name in {{ config('languages.' . getLangCode($sub_operator->id)) }}
                            </label>
                            <input name="company_in_native_lang" type="text"
                                class="form-control @error('company_in_native_lang') is-invalid @enderror"
                                id="company_in_native_lang" value="{{ $sub_operator->company_in_native_lang }}"
                                autocomplete="company_in_native_lang" required>
                            @error('company_in_native_lang')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/company_in_native_lang-->

                        <!-- account_type -->
                        <div class="form-group">
                            <label for="account_type"><span class="text-danger">*</span>Account Type</label>
                            <select name="account_type" id="account_type" class="form-control" required>
                                <option value="{{ $sub_operator->account_type }}" selected>
                                    {{ $sub_operator->account_type_alias }}
                                </option>
                                <option value="credit">Postpaid</option>
                                <option value="debit">Prepaid</option>
                            </select>
                        </div>
                        <!--/account_type -->

                        <!--name-->
                        <div class="form-group">
                            <label for="name"><span class="text-danger">*</span>Name</label>
                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" value="{{ $sub_operator->name }}" autocomplete="name" required>
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
                                id="mobile" value="{{ $sub_operator->mobile }}" autocomplete="mobile" required>
                            @error('mobile')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/mobile-->

                        <!--email-->
                        <div class="form-group">
                            <label for="email"><span class="text-danger">*</span>Email address</label>
                            <input name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" value="{{ $sub_operator->email }}" autocomplete="off" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/email-->

                        <!--password-->
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input name="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" id="password"
                                placeholder="Password" autocomplete="new-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--password-->

                        <button type="submit" class="btn btn-dark">Submit</button>

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
    <script type="text/javascript">
        $(document).ready(function() {

            $('#quickForm').validate({
                onkeyup: false,
                rules: {

                    name: {
                        required: true
                    },

                    mobile: {
                        required: true,
                        minlength: 11
                    }
                },
                messages: {

                    name: {
                        required: "Please enter Name"
                    },

                    mobile: {
                        required: "Please enter Mobile Number",
                        email: "Please enter a vaild mobile number"
                    }
                },

                errorElement: 'span',

                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },

                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },

                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }

            });
        });
    </script>
@endsection
