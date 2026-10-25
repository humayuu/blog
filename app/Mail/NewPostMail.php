<?php

namespace App\Mail;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewPostMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Post $post) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'A New Post Has Been Published!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'new_post',
        );
    }
}
