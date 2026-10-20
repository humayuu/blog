<?php

namespace App\Services\V1;

use App\Enums\UserStatus;
use App\Jobs\UserWelcomeJob;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class UserService
{
    // For Get All Users With pagination
    public function getUsers(int $perPage)
    {
        return User::orderBy('name')->paginate($perPage);
    }

    // For Create User
    public function createUser(array $data, ?UploadedFile $image = null)
    {
        $imagePath = null;

        if ($image) {
            $imagePath = $image->store('uploads', 'users');
        }
        $password = $data['password'];

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $password,
            'avatar' => $imagePath,
        ]);

        UserWelcomeJob::dispatch($user, $password);

        return $user;
    }

    // For Update User
    public function updateUser(string $id, array $data, ?UploadedFile $image = null)
    {
        $user = User::findOrFail($id);

        $oldImage = $user->avatar;
        $imagePath = $oldImage;

        if ($image) {
            $imagePath = $image->store('uploads', 'users');
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'] ?? $user->password,
            'avatar' => $imagePath,
        ]);

        return $user->refresh();
    }

    // For Delete User
    public function deleteUser(string $id)
    {
        $user = User::findOrFail($id);

        return $user->delete();
    }

    // For Toggle User Status
    public function userStatus(string $id)
    {
        $user = User::findOrFail($id);

        $newStatus = ($user->status == UserStatus::ACTIVE->value)
            ? UserStatus::INACTIVE->value
            : UserStatus::ACTIVE->value;

        return $user->update([
            'status' => $newStatus,
        ]);
    }
}
