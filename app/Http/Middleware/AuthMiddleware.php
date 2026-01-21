<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $authKey = $request->header('X-Auth-Key') ?? $request->input('auth_key');
        $validKey = config('app.auth_key');

        if (!$authKey || $authKey !== $validKey) {
            return response()->json(['error' => 'Unauthorized', 'message' => 'Invalid or missing auth key'], 401);
        }

        return $next($request);
    }
}
