@section('contentTitle')
    <h3>Edit Manager</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <p class="text-danger">* required field</p>


            <form id="quickForm" method="POST" action="{{ route('managers.update', ['manager' => $manager->id]) }}">

                @csrf

                @method('put')

                <div class="row">

                    <div class="col-sm-6">

                        <!--name-->
                        <div class="form-group">
                            <label for="name"><span class="text-danger">*</span>Name</label>
                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" value="{{ $manager->name }}" autocomplete="name" required>
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
                                id="mobile" value="{{ $manager->mobile }}" autocomplete="new-password" required>
                            @error('mobile')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/mobile-->

                        <!--password-->
                        <div class="form-group">
                            <label for="password">New Password</label>
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
                    </div>
                    <!--/col-sm-6-->

                    <div class="col-sm-6">

                        <div class="card-header font-weight-bold">Permissions</div>

                        {{-- selected_permission --}}
                        @foreach ($selected_permissions as $selected_permission)
                            <div class="form-check">
                                <input name="permissions[]" class="form-check-input" type="checkbox"
                                    value="{{ $selected_permission }}" id="{{ $selected_permission }}" checked>
                                <label class="form-check-label" for="{{ $selected_permission }}">
                                    {{ $selected_permission }}
                                </label>
                            </div>
                        @endforeach

                        {{-- selected_permission --}}

                        {{-- new_permission --}}
                        @foreach ($new_permissions as $new_permission)
                            <div class="form-check">
                                <input name="permissions[]" class="form-check-input" type="checkbox"
                                    value="{{ $new_permission }}" id="{{ $new_permission }}">
                                <label class="form-check-label" for="{{ $new_permission }}">
                                    {{ $new_permission }}
                                </label>
                            </div>
                        @endforeach
                        {{-- new_permission --}}

                    </div>

                </div>
                <!--/row-->

                <button type="submit" class="btn btn-dark">Submit</button>

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

                    password: {
                        required: false,
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

                    password: {
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
