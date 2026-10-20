<?php

namespace App\Exceptions\User;

use Exception;
use Illuminate\Http\JsonResponse;

class UserNotFoundException extends Exception
{
    /**
     * Render the exception for a user that doesn't exist.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'User Not Found',
        ], 404);
    }
}
