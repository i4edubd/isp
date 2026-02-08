@extends ('laraview.layouts.sideNavLayout')

@section('title')
Group admins
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
@include('admins.super_admin.sidebar')
@endsection


@section('contentTitle')

<h3>Edit Group Admin</h3>

@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <p class="text-danger">* required field</p>

        <div class="row">
            <div class="col-sm-6">
                <form id="quickForm" method="POST"
                    action="{{ route('group_admins.update',['group_admin' => $group_admin->id]) }}">
                    @csrf
                    @method('put')

                    <!--company-->
                    <div class="form-group">
                        <label for="company"><span class="text-danger">*</span>Company Name</label>
                        <input name="company" type="text" class="form-control @error('company') is-invalid @enderror"
                            id="company" value="{{ $group_admin->company }}" required>
                        @error('company')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/company-->

                    <!--name-->
                    <div class="form-group">
                        <label for="name"><span class="text-danger">*</span>Name</label>
                        <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" value="{{ $group_admin->name }}" autocomplete="name" required>
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
                            id="mobile" value="{{ $group_admin->mobile }}" autocomplete="mobile" required>
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
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ $group_admin->email }}" autocomplete="email" required>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/email-->

                    <!-- using_payment_gateway -->
                    <div class="form-group">
                        <label for="using_payment_gateway"><span class="text-danger">*</span>Using Payment
                            Gateway?</label>
                        <select name="using_payment_gateway" id="using_payment_gateway" class="form-control" required>
                            <option value="{{ $group_admin->using_payment_gateway }}" selected>
                                {{ $group_admin->using_payment_gateway }}
                            </option>
                            <option value="0">0</option>
                            <option value="1">1</option>
                        </select>
                    </div>
                    <!--/using_payment_gateway -->

                    <!--password-->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input name="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" id="password"
                            placeholder="Password" autocomplete="current-password">
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

</div>

@endsection

@section('pageJs')

<script type="text/javascript">
    $(document).ready(function () {

        $('#quickForm').validate({
            onkeyup: false,
            rules: {

                company: {
                    required: true
                },

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

            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },

            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },

            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }

        });
    });

</script>

@endsection
