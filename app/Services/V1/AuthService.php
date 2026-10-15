<?php

namespace App\Services\V1;

use App\Exceptions\Auth\InvalidCredentials;
use App\Mail\Auth\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    /**
     * Service for user register
     */
    public function userRegister(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $otp = random_int(100000, 999999);

        Mail::to($user->email)->send(new OtpMail($user));

        return $user;
    }

    /**
     * Service for user login
     */
    public function userLogin(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            throw new InvalidCredentials();
        }

        if (!Hash::check($data['password'], $user->password)) {
            throw new InvalidCredentials();
        }


        return $user;
    }

    /**
     * Service for email verification
     */
    public function verifyEmail(int $userCode, Request $request)
    {
        $cacheKey = 'otp_' . $request->user()->id;
        $code = Cache::get($cacheKey);

        if ($code === $userCode) {
            $request->user()->email_verified_at = now();
        }
    }
}
