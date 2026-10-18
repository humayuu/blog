<?php

namespace App\Mail\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordLink extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $token,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password Link',
        );
    }

    public function content(): Content
    {
        $resetUrl = rtrim(
            config('app.frontend_url', 'http://localhost:3000'),
            '/'
        ) . '/reset-password?' . http_build_query([
            'token' => $this->token,
            'email' => $this->user->email,
        ]);

        return new Content(
            view: 'reset_password',
            with: [
                'userName' => $this->user->name,
                'resetUrl' => $resetUrl,
            ],
        );
    }
}
