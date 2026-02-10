@extends('layouts.demo1.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>
                                <button type="button" id="login-passkey-button" class="btn btn-secondary">
                                    {{ __('Login with Passkey') }}
                                </button>
                                <p id="status"></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const status = document.getElementById('status');

    document.getElementById('login-passkey-button').addEventListener('click', async () => {
        status.textContent = 'Generating login options...';

        try {
            const response = await fetch('{{ route('webauthn.login.options') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            });

            const options = await response.json();

            if (response.status !== 200) {
                throw new Error(options.message);
            }

            status.textContent = 'Please follow your browser instructions to use your passkey.';

            const {
                get
            } = await import('https://cdn.jsdelivr.net/npm/@github/webauthn-json/dist/esm/webauthn-json.js');

            const credential = await get(options);

            const loginResponse = await fetch('{{ route('webauthn.login') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(credential)
            });

            const loginResult = await loginResponse.json();

            if (loginResponse.status !== 200) {
                throw new Error(loginResult.message);
            }

            status.textContent = loginResult.message;
            window.location.href = '/dashboard';
        } catch (error) {
            status.textContent = 'Error: ' + error.message;
        }
    });
</script>
@endsection
