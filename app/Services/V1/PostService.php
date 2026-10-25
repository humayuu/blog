<?php

namespace App\Services\V1;

use App\Jobs\NewPostMailJob;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class PostService
{
    // Get All Posts With Pagination
    public function getPaginate(
        int $perPage = 15
    ): LengthAwarePaginator {
        $perPage = max(1, min($perPage, 100));

        return Post::orderByDesc('id')->paginate($perPage);
    }

    // Create Post
    public function createPost(array $data): Post
    {
        $title = $data['title'];
        $isPublished = $data['is_published'] ?? false;

        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $post = Post::create([
            'user_id' => auth()->id(),
            'category_id' => $data['category_id'],
            'title' => $title,
            'slug' => $slug,
            'short_description' => $data['short_description'] ?? null,
            'content' => $data['content'],
            'featured_image' => $data['featured_image'] ?? null,
            'is_published' => $isPublished,
            'published_at' => $data['published_at']
                ?? ($isPublished ? now() : null),
        ]);

        if ($post->is_published) {
            NewPostMailJob::dispatch($post)->afterCommit();
        }

        return $post;
    }


    // Update Post Service
    public function updatePost(array $data, string $id)
    {
        $post = Post::findOrFail($id);
    }


    // Delete Post Service 
    public function deletePost(string $id) {}
}
