@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('User Profile') }}</div>

                <div class="card-body">
                    <p>Welcome, {{ Auth::user()->name }}!</p>

                    <hr>

                    <h2>Manage Passkeys</h2>
                    <button id="register-button" class="btn btn-primary">Register a New Passkey</button>
                    <p id="status"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('register-button').addEventListener('click', async () => {
        const status = document.getElementById('status');
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
</script>
@endsection

