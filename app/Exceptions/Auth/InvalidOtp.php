<?php

namespace App\Exceptions\Auth;

use Exception;
use Illuminate\Http\JsonResponse;

class InvalidOtp extends Exception
{
    /**
     * Render the exception for Invalid Otp.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Invalid Otp Or Otp expired.',
        ], 400);
    }
}
