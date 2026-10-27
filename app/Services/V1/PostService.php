<?php

namespace App\Services\V1;

use App\Jobs\NewPostMailJob;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class PostService
{
    // Get all posts with pagination
    public function getPaginate(int $perPage = 15): LengthAwarePaginator
    {
        $perPage = max(1, min($perPage, 100));

        return Post::query()
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    // Create post
    public function createPost(array $data, UploadedFile $image): Post
    {
        $title = $data['title'];
        $isPublished = (bool) ($data['is_published'] ?? false);

        $imagePath = $image->store('post', 'public');

        if (! $imagePath) {
            throw new RuntimeException('Failed to upload the post image.');
        }

        try {
            $post = Post::create([
                'user_id' => auth()->id(),
                'category_id' => $data['category_id'],
                'title' => $title,
                'slug' => $this->generateUniqueSlug($title),
                'short_description' => $data['short_description'] ?? null,
                'content' => $data['content'],
                'featured_image' => $imagePath,
                'is_published' => $isPublished,
                'published_at' => $data['published_at']
                    ?? ($isPublished ? now() : null),
            ]);

            if (! $post->exists) {
                throw new RuntimeException('Failed to create the post.');
            }
        } catch (Throwable $exception) {
            // Remove the uploaded image if post creation fails.
            Storage::disk('public')->delete($imagePath);

            throw $exception;
        }

        // Notify subscribers when a post is created as published.
        if ($post->is_published) {
            NewPostMailJob::dispatch($post)->afterCommit();
        }

        return $post;
    }

    // Update post
    public function updatePost(
        array $data,
        string $id,
        ?UploadedFile $image = null
    ): Post {
        $post = Post::findOrFail($id);

        $wasPublished = (bool) $post->is_published;
        $oldImagePath = $post->featured_image;
        $newImagePath = null;

        $title = $data['title'] ?? $post->title;

        // Generate a new slug only when the title changes.
        $slug = $title !== $post->title
            ? $this->generateUniqueSlug($title, $post->getKey())
            : $post->slug;

        $isPublished = (bool) ($data['is_published'] ?? $post->is_published);

        $publishedAt = $data['published_at'] ?? (
            $isPublished ? ($post->published_at ?? now()) : null
        );

        // Upload a replacement image only when one is provided.
        if ($image !== null) {
            $newImagePath = $image->store('post', 'public');

            if (! $newImagePath) {
                throw new RuntimeException(
                    'Failed to upload the replacement post image.'
                );
            }
        }

        try {
            $post->fill([
                'category_id' => $data['category_id'] ?? $post->category_id,
                'title' => $title,
                'slug' => $slug,
                'short_description' => array_key_exists(
                    'short_description',
                    $data
                )
                    ? $data['short_description']
                    : $post->short_description,
                'content' => $data['content'] ?? $post->content,
                'featured_image' => $newImagePath ?? $oldImagePath,
                'is_published' => $isPublished,
                'published_at' => $publishedAt,
            ]);

            if (! $post->save()) {
                throw new RuntimeException('Failed to update the post.');
            }
        } catch (Throwable $exception) {
            // Remove the new upload if updating the database fails.
            if ($newImagePath !== null) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        // Delete the previous image only after the update succeeds.
        if (
            $newImagePath !== null
            && $oldImagePath !== null
            && $oldImagePath !== $newImagePath
        ) {
            Storage::disk('public')->delete($oldImagePath);
        }

        // Send the notification only when the post becomes published.
        if (! $wasPublished && $post->is_published) {
            NewPostMailJob::dispatch($post)->afterCommit();
        }

        return $post;
    }

    // Delete post
    public function deletePost(string $id): bool
    {
        $post = Post::findOrFail($id);

        $imagePath = $post->featured_image;

        // Delete the database record first.
        if (! $post->delete()) {
            return false;
        }

        // Delete the associated image after successful deletion.
        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        return true;
    }

    // Generate a unique slug
    private function generateUniqueSlug(
        string $title,
        int|string|null $ignorePostId = null
    ): string {
        $baseSlug = Str::slug($title) ?: 'post';
        $slug = $baseSlug;
        $counter = 1;

        while (
            Post::query()
            ->where('slug', $slug)
            ->when(
                $ignorePostId !== null,
                fn($query) => $query->where('id', '!=', $ignorePostId)
            )
            ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }
}
