<?php

namespace App\Exceptions\Auth;

use Exception;
use Illuminate\Http\JsonResponse;

class AlreadyVerified extends Exception
{
    /**
     * Render the exception for an already verified account.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Email is already verified.',
        ], 400);
    }
}
