<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\CompletePasswordResetRequest;
use App\Http\Requests\V1\Auth\EmailVerificationRequest;
use App\Http\Requests\V1\Auth\ResetPasswordRequest;
use App\Http\Requests\V1\Auth\UserLoginRequest;
use App\Http\Requests\V1\Auth\UserRegisterRequest;
use App\Http\Resources\V1\UserResource;
use App\Services\V1\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}


    // Handle user register
    public function register(UserRegisterRequest $request)
    {
        $user = $this->authService->userRegister($request->validated());

        // create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User Created Successfully.Please verify your email first',
            'token_type' => 'bearer',
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    // Handle user login
    public function login(UserLoginRequest $request)
    {
        $user = $this->authService->userLogin($request->validated());

        // create token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User Logged In Successfully.',
            'token_type' => 'bearer',
            'user' => new UserResource($user),
            'token' => $token,
        ], 200);
    }

    // Handle user logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Successfully logged out and token deleted.'
        ], 200);
    }

    // Handle authenticated User
    public function authenticatedUser(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => true,
            'user' => new UserResource($user),
        ], 200);
    }

    // Handle email verification
    public function emailVerify(EmailVerificationRequest $request)
    {
        $this->authService->verifyEmail(
            (string) $request->validated('otp'),
            $request->user()
        );

        return response()->json([
            'status' => true,
            'message' => 'Email Verified Successfully',
        ], 200);
    }

    // Handle Resend Otp 
    public function resendOtp(Request $request)
    {
        $this->authService->resendOtpMail($request->user());

        return response()->json([
            'status' => true,
            'message' => 'Resend Otp Successfully',
        ], 200);
    }

    public function resetPasswordLink(ResetPasswordRequest $request)
    {
        $this->authService->sendPasswordResetLink(
            $request->validated()['email']
        );

        return response()->json([
            'status' => true,
            'message' => 'If an account exists for this email, a reset link has been sent.',
        ], 200);
    }

    public function completePasswordReset(
        CompletePasswordResetRequest $request
    ) {
        $status = $this->authService->completePasswordReset(
            $request->validated()
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired password reset token.',
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully.',
        ], 200);
    }
}
