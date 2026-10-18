<?php

namespace App\Services\V1;

use App\Exceptions\Auth\AlreadyVerified;
use App\Exceptions\Auth\InvalidCredentials;
use App\Exceptions\Auth\InvalidOtp;
use App\Exceptions\Auth\OtpResendTooSoon;
use App\Jobs\ResetPassword;
use App\Jobs\SendOtpEmail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset as PasswordResetEvent;

class AuthService
{
    private const OTP_MAX_ATTEMPTS = 5;

    private const OTP_RESEND_COOLDOWN_SECONDS = 60;

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

        $this->issueOtp($user);

        return $user;
    }

    /**
     * Authenticate a user.
     */
    public function userLogin(array $data): User
    {
        $user = User::where('email', $data['email'])->first();

        // Always run a hash check so response time doesn't reveal
        // whether the email is registered.
        $hash = $user?->password ?? Hash::make(Str::random(16));

        if (
            !Hash::check($data['password'], $hash) ||
            !$user
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
        $attemptsKey = 'otp_attempts_' . $user->id;

        $code = Cache::get($cacheKey);

        // Too many wrong guesses: burn the OTP so it can't be brute-forced.
        if ((int) Cache::get($attemptsKey, 0) >= self::OTP_MAX_ATTEMPTS) {
            Cache::forget($cacheKey);
            throw new InvalidOtp();
        }

        if (
            !is_string($code) ||
            !hash_equals($code, $this->hashOtp($userCode))
        ) {
            Cache::add($attemptsKey, 0, now()->addMinutes(10));
            Cache::increment($attemptsKey);

            throw new InvalidOtp();
        }

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        Cache::forget($cacheKey);
        Cache::forget($attemptsKey);
    }

    /**
     * Resend Otp
     */
    public function resendOtpMail(User $user): void
    {
        if ($user->email_verified_at !== null) {
            throw new AlreadyVerified();
        }

        // Cooldown stops resend from being used to reset the attempt counter.
        if (!Cache::add(
            'otp_resend_' . $user->id,
            1,
            now()->addSeconds(self::OTP_RESEND_COOLDOWN_SECONDS)
        )) {
            throw new OtpResendTooSoon();
        }

        $this->issueOtp($user);
    }

    private function hashOtp(string $otp): string
    {
        return hash_hmac('sha256', $otp, (string) config('app.key'));
    }

    /**
     * Generate a fresh OTP, reset the attempt counter and email it.
     */
    private function issueOtp(User $user): void
    {
        $otp = (string) random_int(100000, 999999);

        // Only a keyed hash of the OTP is cached, never the plaintext.
        Cache::put('otp_' . $user->id, $this->hashOtp($otp), now()->addMinutes(10));
        Cache::forget('otp_attempts_' . $user->id);

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
