@section('contentTitle')
    <h3>New Manager</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <p class="text-danger">* required field</p>


            <form id="quickForm" method="POST" action="{{ route('managers.store') }}">
                @csrf

                <div class="row">

                    <div class="col-sm-6">

                        <!--name-->
                        <div class="form-group">
                            <label for="name"><span class="text-danger">*</span>Name</label>
                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" value="{{ old('name') }}" autocomplete="name" required>
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
                                id="mobile" value="{{ old('mobile') }}" autocomplete="mobile" required>
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
                                id="email" value="{{ old('email') }}" autocomplete="new-password" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/email-->

                        <!--password-->
                        <div class="form-group">
                            <label for="password"><span class="text-danger">*</span>Password</label>
                            <input name="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" id="password"
                                placeholder="Password" autocomplete="new-password" required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--password-->
                    </div>
                    <!--/col-sm-6-->

                    <div class="col-sm-6">

                        <div class="card-header font-weight-bold">Permissions</div>

                        {{-- permissions --}}
                        @foreach ($permissions as $permission)
                            <div class="form-check">
                                <input name="permissions[]" class="form-check-input" type="checkbox"
                                    value="{{ $permission }}" id="{{ $permission }}">
                                <label class="form-check-label" for="{{ $permission }}">
                                    {{ $permission }}
                                </label>
                            </div>
                        @endforeach

                        {{-- permissions --}}

                    </div>

                </div>
                <!--/row-->

                <button type="submit" class="btn btn-dark mt-2">Submit</button>

            </form>

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
                    },

                    email: {
                        required: true,
                        email: true
                    },

                    password: {
                        required: true,
                        minlength: 8
                    }

                },
                messages: {

                    name: {
                        required: "Please enter Name"
                    },

                    mobile: {
                        required: "Please enter Mobile Number",
                        email: "Please enter a vaild mobile number"
                    },

                    email: {
                        required: "Please enter a email address",
                        email: "Please enter a vaild email address"
                    },

                    password: {
                        required: "Please provide a password",
                        minlength: "password must be at least 8 characters long"
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
