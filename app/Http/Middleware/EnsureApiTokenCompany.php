<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiTokenCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $token = $user?->currentAccessToken();

        if (! $user || ! $token) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if ($token instanceof PersonalAccessToken) {
            if (
                ! $token->company_id
                || ! $user->company_id
                || $token->company_id !== $user->company_id
            ) {
                return response()->json([
                    'message' => 'Token is not valid for the current company.',
                ], 403);
            }
        }

        return $next($request);
    }
}