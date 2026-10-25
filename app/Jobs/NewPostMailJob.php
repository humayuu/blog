<?php

namespace App\Jobs;

use App\Mail\NewPostMail;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class NewPostMailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Post $post) {}

    public function handle(): void
    {
        User::query()
            ->select(['id', 'email'])
            ->whereNotNull('email')
            ->chunkById(500, function ($users): void {
                foreach ($users as $user) {
                    Mail::to($user->email)
                        ->send(new NewPostMail($this->post));
                }
            });
    }
}
