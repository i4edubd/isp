<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WebAuthn Demo</title>
    <style>
        body { font-family: sans-serif; display: flex; flex-direction: column; align-items: center; margin-top: 50px; }
        .container { border: 1px solid #ccc; padding: 20px; border-radius: 8px; }
        input, button { font-size: 1em; margin: 5px; padding: 10px; }
        #status { margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>ISP Bills - WebAuthn Demo</h1>
        <div>
            <label for="email">Email (used to identify user)</label>
            <br>
            <input type="email" id="email" name="email" value="test@example.com" required>
        </div>
        <div>
            <button id="registerButton">Register Passkey</button>
            <button id="loginButton">Login with Passkey</button>
        </div>
        <div id="status"></div>
    </div>

    <script>
        const registerButton = document.getElementById('registerButton');
        const loginButton = document.getElementById('loginButton');
        const emailInput = document.getElementById('email');
        const statusEl = document.getElementById('status');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Helper functions to handle Base64URL encoding/decoding
        function bufferDecode(value) {
            return new Uint8Array(atob(value).split('').map(c => c.charCodeAt(0)));
        }

        function bufferEncode(value) {
            return btoa(String.fromCharCode.apply(null, new Uint8Array(value)))
                .replace(/\+/g, "-")
                .replace(/\//g, "_")
                .replace(/=/g, "");
        }

        registerButton.addEventListener('click', async () => {
            statusEl.textContent = '';
            const email = emailInput.value;
            if (!email) {
                statusEl.textContent = 'Please enter an email address.';
                return;
            }

            // 1. Get registration options from the server
            const resp = await fetch('/webauthn/register/options', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ email }),
            });

            if (!resp.ok) {
                statusEl.textContent = 'Error: Could not get registration options.';
                return;
            }

            let options = await resp.json();
            
            // Turn base64url encoded strings into ArrayBuffers
            options.challenge = bufferDecode(options.challenge);
            options.user.id = bufferDecode(options.user.id);

            // 2. Call navigator.credentials.create()
            let credential;
            try {
                credential = await navigator.credentials.create({ publicKey: options });
            } catch (err) {
                statusEl.textContent = 'Error: ' + err.message;
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
            statusEl.textContent = verificationJson.message;
        });

        loginButton.addEventListener('click', async () => {
            statusEl.textContent = '';

            // 1. Get login options from the server
            const resp = await fetch('/webauthn/login/options', {
                method: 'POST', // Still a POST for consistency and CSRF
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            });

             if (!resp.ok) {
                statusEl.textContent = 'Error: Could not get login options.';
                return;
            }
            
            let options = await resp.json();

            // Turn base64url encoded strings into ArrayBuffers
            options.challenge = bufferDecode(options.challenge);
            
            // 2. Call navigator.credentials.get()
            let credential;
            try {
                credential = await navigator.credentials.get({ publicKey: options });
            } catch (err) {
                statusEl.textContent = 'Error: ' + err.message;
                return;
            }

            // 3. Send the assertion to the server for verification
             const assertionResponse = {
                id: credential.id,
                rawId: bufferEncode(credential.rawId),
                type: credential.type,
                response: {
                    authenticatorData: bufferEncode(credential.response.authenticatorData),
                    clientDataJSON: bufferEncode(credential.response.clientDataJSON),
                    signature: bufferEncode(credential.response.signature),
                    userHandle: bufferEncode(credential.response.userHandle),
                },
            };

            const verificationResp = await fetch('/webauthn/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(assertionResponse),
            });

            const verificationJson = await verificationResp.json();
            statusEl.textContent = verificationJson.message;
        });
    </script>
</body>
</html>