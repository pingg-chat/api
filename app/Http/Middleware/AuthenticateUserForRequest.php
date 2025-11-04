<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateUserForRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->header('X-Auth-User-ID');

        Auth::loginUsingId($userId);

        return $next($request);
    }
}
