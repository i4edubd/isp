<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/fontawesome-free/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="/themes/adminlte3x/plugins/toastr/toastr.min.css">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="/themes/adminlte3x/dist/css/adminlte.min.css">
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css?family=Numans');

        html,
        body {
            background-image: url({{ $background_image }});
            background-size: cover;
            background-repeat: no-repeat;
            height: 100%;
            font-family: 'Numans', sans-serif;
        }

        .container {
            height: 100%;
            align-content: center;
        }

        .card {
            height: 370px;
            margin-top: auto;
            margin-bottom: auto;
            width: 400px;
            background-color: rgba(0, 0, 0, 0.5) !important;
        }

        .social_icon span {
            font-size: 60px;
            margin-left: 10px;
            color: #FFC312;
        }

        .social_icon span:hover {
            color: white;
            cursor: pointer;
        }

        .card-header h3 {
            color: white;
        }

        .social_icon {
            position: absolute;
            right: 20px;
            top: -45px;
        }

        .input-group-prepend span {
            width: 50px;
            background-color: #FFC312;
            color: black;
            border: 0 !important;
        }

        input:focus {
            outline: 0 0 0 0 !important;
            box-shadow: 0 0 0 0 !important;

        }

        .remember {
            color: white;
        }

        .remember input {
            width: 20px;
            height: 20px;
            margin-left: 15px;
            margin-right: 5px;
        }

        .login_btn {
            color: black;
            background-color: #FFC312;
            width: 100px;
        }

        .login_btn:hover {
            color: black;
            background-color: white;
        }

        .links {
            color: white;
        }

        .links a {
            margin-left: 4px;
        }
    </style>

</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-center h-100">
            <div class="card">
                <div class="card-header">
                    <h3>Sign In ( {{ $category }} )</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ $login_route }}">
                        @csrf
                        <!--email-->
                        <div class="input-group form-group">

                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>

                            <input name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                placeholder="{{ $placeholder }}" required>

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                        </div>
                        <!--/email-->

                        <!--password-->
                        <div class="input-group form-group">

                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                            </div>

                            <input name="password" type="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="password"
                                required autocomplete="current-password">

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                        </div>
                        <!--/password-->

                        <!--Remember Me-->
                        <div class="row align-items-center remember">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            Remember Me
                        </div>
                        <!--/Remember Me-->

                        <!--submit-->
                        <div class="form-group">
                            <input type="submit" value="Login" class="btn float-right login_btn">
                        </div>
                        <!--/submit-->

                    </form>
                </div>
                <div class="card-footer">

                    <!--Forgot password-->
                    @if ($category === 'admin')
                    <div class="d-flex justify-content-center">
                        <a class="btn btn-link" href="{{ route('password.request') }}">Forgot your password?</a>
                    </div>
                    @endif
                    <!--/Forgot password-->

                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="/themes/adminlte3x/plugins/jquery/jquery.min.js"></script>
    <!--jquery-ui-1.12.1-->
    <script src="/themes/adminlte3x/plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="/themes/adminlte3x/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="/themes/adminlte3x/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Toastr -->
    <script src="/themes/adminlte3x/plugins/toastr/toastr.min.js"></script>

    @if (session('success'))
    <script type="text/javascript">
        $(function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });

                Toast.fire({
                    icon: 'success',
                    title: '{{ session(' success') }}'
                })
            });

    </script>
    @endif

    @if (session('error'))
    <script type="text/javascript">
        $(function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 6000
                });

                Toast.fire({
                    icon: 'error',
                    title: '{{ session('error') }}'
                })
            });
    </script>
    @endif

</body>

</html>
