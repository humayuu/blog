<?php

namespace App\Services\V1;

use App\Exceptions\Auth\InvalidCredentials;
use App\Exceptions\Auth\InvalidOtp;
use App\Jobs\SendOtpEmail;
use App\Mail\Auth\OtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
}
