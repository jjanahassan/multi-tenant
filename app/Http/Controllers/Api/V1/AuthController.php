<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PersonalAccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function token(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:255'],
        ]);

        $user = \App\Models\User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->company_id) {
            throw ValidationException::withMessages([
                'email' => ['The user is not associated with a company.'],
            ]);
        }

        $plainTextToken = Str::random(40);

        $accessToken = new PersonalAccessToken();

        $accessToken->forceFill([
            'name' => $validated['device_name'],
            'token' => hash('sha256', $plainTextToken),
            'abilities' => ['*'],
            'expires_at' => null,
            'company_id' => $user->company_id,
        ]);

        $user->tokens()->save($accessToken);

        return response()->json([
            'token' => $accessToken->getKey().'|'.$plainTextToken,
            'token_type' => 'Bearer',
            'company_id' => $user->company_id,
        ]);
    }
}