<?php

namespace App\Exceptions\Auth;

use Exception;
use Illuminate\Http\JsonResponse;

class OtpResendTooSoon extends Exception
{
    /**
     * Render the exception for an OTP resend requested too soon.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Please wait a minute before requesting another Otp.',
        ], 429);
    }
}
