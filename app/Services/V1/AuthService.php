<?php

namespace App\Services\V1;

use App\Exceptions\Auth\InvalidCredentials;
use App\Exceptions\Auth\InvalidOtp;
use App\Jobs\ResetPassword;
use App\Jobs\SendOtpEmail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use App\Jobs\SendResetPasswordLink;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset as PasswordResetEvent;

class AuthService
{
    /**
     * Register a user and send an email verification OTP.
     */
    public function userRegister(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $otp = (string) random_int(100000, 999999);

        $cacheKey = 'otp_' . $user->id;

        Cache::put($cacheKey, $otp, now()->addMinutes(10));

        SendOtpEmail::dispatch($user, $otp);

        return $user;
    }

    /**
     * Authenticate a user.
     */
    public function userLogin(array $data): User
    {
        $user = User::where('email', $data['email'])->first();

        if (
            !$user ||
            !Hash::check($data['password'], $user->password)
        ) {
            throw new InvalidCredentials();
        }

        return $user;
    }

    /**
     * Verify the user's email using the OTP.
     */
    public function verifyEmail(string $userCode, User $user): void
    {
        $cacheKey = 'otp_' . $user->id;
        $code = Cache::get($cacheKey);

        if (
            !is_string($code) ||
            !hash_equals($code, $userCode)
        ) {
            throw new InvalidOtp();
        }

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        Cache::forget($cacheKey);
    }

    /**
     * Resend Otp
     */
    public function resendOtpMail(User $user)
    {
        $cacheKey = 'otp_' . $user->id;

        if (Cache::has($cacheKey)) {
            Cache::forget($cacheKey);
        }

        $otp = (string) random_int(100000, 999999);

        Cache::put($cacheKey, $otp, now()->addMinutes(10));

        SendOtpEmail::dispatch($user, $otp);
    }


    public function sendPasswordResetLink(string $email): void
    {
        Password::sendResetLink(
            ['email' => $email],
            function (User $user, string $token): void {
                ResetPassword::dispatch($user, $token);
            }
        );
    }

    public function completePasswordReset(array $data): string
    {
        return Password::reset($data, function (
            User $user,
            string $password
        ): void {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();

            // Revoke existing Sanctum API tokens after password reset.
            $user->tokens()->delete();

            event(new PasswordResetEvent($user));
        });
    }
}
