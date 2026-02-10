@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('WebAuthn') }}</div>

<div class="card-body">
                    <button id="register-button" class="btn btn-primary">Register Passkey</button>
                    <button id="login-button" class="btn btn-secondary">Login with Passkey</button>
                    <p id="status"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const status = document.getElementById('status');

    document.getElementById('register-button').addEventListener('click', async () => {
        status.textContent = 'Generating registration options...';

        try {
            const response = await fetch('{{ route('webauthn.register.options') }}', {
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

            status.textContent = 'Please follow your browser instructions to create a passkey.';

            const {
                create
            } = await import('https://cdn.jsdelivr.net/npm/@github/webauthn-json/dist/esm/webauthn-json.js');

            const credential = await create(options);

            const registerResponse = await fetch('{{ route('webauthn.register') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(credential)
            });

            const registerResult = await registerResponse.json();

            if (registerResponse.status !== 200) {
                throw new Error(registerResult.message);
            }

            status.textContent = registerResult.message;
        } catch (error) {
            status.textContent = 'Error: ' + error.message;
        }
    });

    document.getElementById('login-button').addEventListener('click', async () => {
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
            // You might want to redirect the user to their dashboard here
            // window.location.href = '/dashboard';
        } catch (error) {
            status.textContent = 'Error: ' + error.message;
        }
    });
</script>
