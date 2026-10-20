<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Exceptions\User\UserNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\User\StoreUserRequest;
use App\Http\Requests\V1\User\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Services\V1\UserService;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    // Handle All Users With Filter and Search
    public function index()
    {
        $users = $this->userService->getUsers(5);

        return response()->json([
            'status' => true,
            'message' => 'User Fetched Successfully',
            'users' => UserResource::collection($users),
        ], 200);
    }

    // Handle Store Users
    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser(
            $request->validated(),
            $request->file('avatar')
        );

        return response()->json([
            'status' => true,
            'message' => 'User Created Successfully',
            'user' => new UserResource($user),
        ], 201);
    }

    // Handle For Get Specific User
    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return response()->json([
            'status' => true,
            'message' => 'User Fetched Successfully',
            'user' => new UserResource($user),
        ], 200);
    }

    // Handle Users Update
    public function update(string $id, UpdateUserRequest $request)
    {
        $user = $this->userService->updateUser(
            $id,
            $request->validated(),
            $request->file('avatar')
        );

        return response()->json([
            'status' => true,
            'message' => 'User Updated Successfully',
            'user' => new UserResource($user),
        ], 200);
    }

    // Handle Delete Users
    public function delete(string $id)
    {
        $this->userService->deleteUser($id);

        return response()->json([
            'status' => true,
            'message' => 'User Deleted Successfully',
        ], 200);
    }

    // Handle user status
    public function status(string $id)
    {
        $this->userService->userStatus($id);

        return response()->json([
            'status' => true,
            'message' => 'User Status Updated Successfully',
        ], 200);
    }
}
