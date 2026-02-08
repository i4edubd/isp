<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Login - ISP Bills</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Custom CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,170.7C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') bottom center no-repeat;
            background-size: cover;
            pointer-events: none;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            text-align: center;
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-container {
            margin-bottom: 30px;
        }

        .logo-icon {
            width: 120px;
            height: 120px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .login-container h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #2d3748;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #718096;
            margin-bottom: 35px;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 16px;
            z-index: 1;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f7fafc;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-control.is-invalid {
            border-color: #fc8181;
            background: #fff5f5;
        }

        .invalid-feedback {
            display: block;
            color: #e53e3e;
            font-size: 13px;
            text-align: left;
            margin-top: 5px;
            padding-left: 20px;
        }

        .btn-primary {
            width: 100%;
            padding: 15px 20px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            margin-top: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 25px;
            text-align: left;
        }

        .checkbox-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            cursor: pointer;
            accent-color: #667eea;
        }

        .checkbox-label label {
            color: #4a5568;
            font-size: 14px;
            cursor: pointer;
            user-select: none;
        }

        .login-footer {
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
        }

        .login-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .login-footer a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .login-footer a + a {
            margin-top: 10px;
            display: inline-block;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 40px 30px;
            }

            .login-container h1 {
                font-size: 24px;
            }

            .logo-icon {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="logo-container">
            <div class="logo-icon">
                <img src="https://docs.ispbills.com/~gitbook/image?url=https%3A%2F%2F2866611805-files.gitbook.io%2F%7E%2Ffiles%2Fv0%2Fb%2Fgitbook-x-prod.appspot.com%2Fo%2Fspaces%252FDvkRzySjjlZXxYbDYpKE%252Flogo%252FbFa21Mg0jhur0e16rkOU%252Fblack-png.webp%3Falt%3Dmedia%26token%3D6fe8fed1-6640-48d5-af4d-26c86f636d60&width=260&dpr=1&quality=100&sign=aee6b53d&sv=2" alt="ISP Bills Logo" loading="eager" decoding="async">
            </div>
            <h1>Admin Login</h1>
            <p class="login-subtitle">Welcome back! Please login to your account.</p>
        </div>
        
        <form method="POST" action="{{ route('login') }}" onsubmit="return disableDuplicateSubmit()">
            @csrf
            
            <div class="input-group">
                <i class="fas fa-envelope input-icon" aria-hidden="true"></i>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                       placeholder="Enter your email" value="{{ old('email') }}" required autofocus 
                       autocomplete="username" aria-label="Email address">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="input-group">
                <i class="fas fa-lock input-icon" aria-hidden="true"></i>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                       placeholder="Enter your password" required autocomplete="current-password" 
                       aria-label="Password">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="checkbox-label">
                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">Keep me signed in</label>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-sign-in-alt"></i> Login to Dashboard
            </button>
        </form>

        <div class="login-footer">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    <i class="fas fa-key"></i> Forgot Password?
                </a>
            @endif
            @if (config('consumer.app_registration'))
                <br>
                <a href="{{ route('register') }}">
                    <i class="fas fa-user-plus"></i> Register a new membership
                </a>
            @endif
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script type="text/javascript">
        function disableDuplicateSubmit() {
            let selector = ".btn-primary";
            $(selector).prop('disabled', true);
            $(selector).html('<i class="fas fa-circle-notch fa-spin"></i> Signing in...');
            return true;
        }

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 6000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    </script>

    @if (session('success'))
        <script type="text/javascript">
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            })
        </script>
    @endif

    @if (session('error'))
        <script type="text/javascript">
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            })
        </script>
    @endif

    @if (session('info'))
        <script type="text/javascript">
            Toast.fire({
                icon: 'info',
                title: '{{ session('info') }}'
            })
        </script>
    @endif

    @if (session('warning'))
        <script type="text/javascript">
            Toast.fire({
                icon: 'warning',
                title: '{{ session('warning') }}'
            })
        </script>
    @endif

</body>

</html>
