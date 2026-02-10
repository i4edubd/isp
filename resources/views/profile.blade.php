<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Profile</title>
    <style>
        body { font-family: sans-serif; display: flex; flex-direction: column; align-items: center; margin-top: 50px; }
        .container { border: 1px solid #ccc; padding: 20px; border-radius: 8px; }
        button { font-size: 1em; margin: 5px; padding: 10px; }
        #status { margin-top: 20px; font-weight: bold; }
        .logout-form { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Profile</h1>
        <p>Welcome, {{ Auth::user()->name }}!</p>

        <hr>

        <h2>Manage Passkeys</h2>
        <button id="registerButton">Register a New Passkey</button>
        <div id="status"></div>

        <form class="logout-form" method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>

    <script>
        const registerButton = document.getElementById('registerButton');
        const statusEl = document.getElementById('status');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Helper functions to handle Base64URL encoding/decoding
        function bufferDecode(value) {
            const b64 = value.replace(/-/g, '+').replace(/_/g, '/');
            const str = atob(b64);
            const array = new Uint8Array(str.length);
            for (let i = 0; i < str.length; i++) {
                array[i] = str.charCodeAt(i);
            }
            return array.buffer;
        }

        function bufferEncode(value) {
            return btoa(String.fromCharCode.apply(null, new Uint8Array(value)))
                .replace(/\+/g, "-")
                .replace(/\//g, "_")
                .replace(/=/g, "");
        }

        registerButton.addEventListener('click', async () => {
            statusEl.textContent = '';
            
            // 1. Get registration options from the server (user is already authenticated)
            const resp = await fetch('/webauthn/register/options', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({}), // Body is empty, server gets user from session
            });

            if (!resp.ok) {
                statusEl.textContent = 'Error: Could not get registration options from server.';
                return;
            }

            let options = await resp.json();
            
            // Turn base64url encoded strings into ArrayBuffers
            options.challenge = bufferDecode(options.challenge);
            options.user.id = bufferDecode(options.user.id);
            if (options.excludeCredentials) {
                options.excludeCredentials.forEach(cred => {
                    cred.id = bufferDecode(cred.id);
                });
            }

            // 2. Call navigator.credentials.create()
            let credential;
            try {
                credential = await navigator.credentials.create({ publicKey: options });
            } catch (err) {
                statusEl.textContent = 'Registration failed or was cancelled. ' + err.message;
                return;
            }

            // 3. Send the credential to the server for verification
            const attestationResponse = {
                id: credential.id,
                rawId: bufferEncode(credential.rawId),
                type: credential.type,
                response: {
                    attestationObject: bufferEncode(credential.response.attestationObject),
                    clientDataJSON: bufferEncode(credential.response.clientDataJSON),
                },
            };

            const verificationResp = await fetch('/webauthn/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(attestationResponse),
            });
            
            const verificationJson = await verificationResp.json();
            
            if (verificationResp.ok) {
                 statusEl.textContent = 'Passkey registered successfully!';
            } else {
                 statusEl.textContent = verificationJson.message;
            }
        });
    </script>
</body>
</html>
