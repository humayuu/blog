<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Post\StorePostRequest;
use App\Http\Requests\V1\Post\UpdatePostRequest;
use App\Services\V1\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    // Get All Posts With Pagination
    public function get(Request $request)
    {
        $validated = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $posts = $this->postService->getPaginate(
            (int) ($validated['per_page'] ?? 15)
        );

        return response()->json($posts);
    }

    // Store New Post
    public function store(StorePostRequest $request)
    {
        $post = $this->postService->createPost($request->validated());

        return response()->json([
            'message' => 'Post created successfully.',
            'data' => $post,
        ], 201);
    }

    // Get Specific Post
    public function show() {}

    // Handle Update Post
    public function update(UpdatePostRequest $request) {}

    // Handle Delete Post
    public function delete() {}
}
