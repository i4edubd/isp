<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laragear\WebAuthn\Facades\WebAuthn;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WebAuthnController extends Controller
{
    /**
     * Generate WebAuthn registration options for the authenticated user.
     * In a real application, a user would first log in with a password
     * to reach this point and add a passkey to their account.
     */
    public function generateRegistrationOptions(Request $request)
    {
        return WebAuthn::generateAttestation($request->user());
    }

    /**
     * Register a new WebAuthn credential for the currently authenticated user.
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|string',
            'rawId' => 'required|string',
            'type' => 'required|string',
            'response' => 'required|array',
        ]);

        try {
            // The package will validate the attestation against the currently
            // authenticated user.
            WebAuthn::validateAttestation($validatedData);
            return response()->json(['message' => 'Passkey registered successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to register passkey: ' . $e->getMessage()], 422);
        }
    }

    /**
     * Generate WebAuthn login options (assertion).
     * This method does not need to know the user in advance for discoverable credentials.
     */
    public function generateLoginOptions()
    {
        return WebAuthn::generateAssertion();
    }

    /**
     * Authenticate a user with WebAuthn.
     */
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|string',
            'rawId' => 'required|string',
            'type' => 'required|string',
            'response' => 'required|array',
        ]);

        // The validateAssertion method will find the user based on the credential ID
        // and return the user model on successful validation.
        $user = WebAuthn::validateAssertion($validatedData);

        if ($user) {
            Auth::login($user);
            return response()->json(['message' => 'Logged in successfully.']);
        }

        return response()->json(['message' => 'Invalid passkey.'], 401);
    }
}