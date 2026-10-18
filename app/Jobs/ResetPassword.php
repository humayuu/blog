<?php

namespace App\Jobs;

use App\Mail\Auth\ResetPasswordLink;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class ResetPassword implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public string $token,
    ) {}

    public function handle(): void
    {
        Mail::to($this->user->email)->send(
            new ResetPasswordLink(
                $this->user,
                $this->token
            )
        );
    }
}
