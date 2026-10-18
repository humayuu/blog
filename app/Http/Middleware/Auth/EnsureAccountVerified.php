<?php

namespace App\Http\Middleware\Auth;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->email_verified_at === null) {
            return response()->json([
                'status' => false,
                'message' => 'Please Verify your email first',
            ], 403);
        }

        return $next($request);
    }
}
