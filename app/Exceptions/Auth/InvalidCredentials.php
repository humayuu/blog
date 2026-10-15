<?php

namespace App\Exceptions\Auth;

use Exception;
use Illuminate\Http\JsonResponse;

class InvalidCredentials extends Exception
{
    /**
     * Render the exception for Invalid credentials.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Invalid Credentials',
        ], 401);
    }
}
