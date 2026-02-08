<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration | {{ config('consumer.app_subscriber') }}</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/fontawesome-free/css/all.min.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/sweetalert2/sweetalert2.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/toastr/toastr.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/themes/adminlte3x/dist/css/adminlte.min.css">
</head>

<body class="hold-transition register-page">

    <!-- Modal -->
    <div class="modal fade" id="ModalShowWait" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="overlay-wrapper">
                        <div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>
                        <div class="text-bold pt-2">Loading...</div>
                        <div class="text-bold pt-2">Please Wait</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/modal-->

    <div class="register-box">

        <div class="card card-outline card-primary">

            <div class="card-header text-center">
                <a href="#" class="h1"><b>{{ config('consumer.app_subscriber') }}</b></a>
            </div>

            <div class="card-body">

                <p class="login-box-msg">Register a new membership</p>

                <form action="{{ route('register') }}" method="post" onsubmit="return showWait()">

                    @csrf

                    {{-- country_id --}}
                    <div class="form-group">
                        <label for="country_id">Country</label>
                        <select class="form-control" id="country_id" name="country_id"
                            hx-get="{{ route('ajax.timezones') }}" hx-target="#timezone" required>
                            <option value="">select...</option>
                            @foreach ($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- country_id --}}

                    {{-- lang_code --}}
                    <div class="form-group">
                        <label for="lang_code">Language</label>
                        <select class="form-control" id="lang_code" name="lang_code" required>
                            @foreach ($lang_codes as $lang_code => $language)
                            <option value="{{ $lang_code }}">{{ $language }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- lang_code --}}

                    {{-- timezone --}}
                    <div class="form-group">
                        <label for="timezone">Time zone</label>
                        <select class="form-control" id="timezone" name="timezone" required>
                        </select>
                    </div>
                    {{-- timezone --}}

                    {{-- company --}}
                    <div class="input-group mb-3">
                        <input type="text" name="company" class="form-control" placeholder="Company Name" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="far fa-building"></span>
                            </div>
                        </div>
                    </div>
                    {{-- company --}}

                    {{-- name --}}
                    <div class="input-group mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Full name" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    {{-- name --}}

                    {{-- mobile --}}
                    <div class="input-group mb-3">
                        <input type="text" name="mobile" class="form-control" placeholder="Mobile" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-phone"></span>
                            </div>
                        </div>
                    </div>
                    {{-- mobile --}}

                    {{-- email --}}
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    {{-- email --}}

                    {{-- password --}}
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password"
                            autocomplete="new-password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    {{-- password --}}

                    {{-- Confirm Password --}}
                    <div class="input-group mb-3">
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="Retype password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    {{-- Confirm Password --}}

                    <div class="row">
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </div>
                    </div>

                </form>

                <a href="{{ route('login') }}" class="text-center">I already have a membership</a>

            </div>
            <!-- /.form-box -->

        </div><!-- /.card -->

    </div>
    <!-- /.register-box -->
    <!-- jQuery -->
    <script src="/themes/adminlte3x/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="/themes/adminlte3x/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="/themes/adminlte3x/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Toastr -->
    <script src="/themes/adminlte3x/plugins/toastr/toastr.min.js"></script>
    <!-- AdminLTE App -->
    <script src="/themes/adminlte3x/dist/js/adminlte.js"></script>
    <script src="/js/htmx.min.js"></script>
    <script>
        function showWait()
            {
                $('#ModalShowWait').modal({
                    backdrop: 'static',
                    show: true
                });
                return true;
            }
    </script>
</body>

</html>