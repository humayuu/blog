<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Enums\PermissionEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached permissions
        app()[PermissionRegistrar::class]
            ->forgetCachedPermissions();

        // Create all permissions from the Enum
        foreach (PermissionEnum::cases() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission->value,
                'guard_name' => 'sanctum',
            ]);
        }

        // Create all roles from the Enum
        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate([
                'name' => $role->value,
                'guard_name' => 'sanctum',
            ]);
        }

        // Get roles
        $superAdmin = Role::findByName(
            RoleEnum::SUPER_ADMIN->value,
            'sanctum'
        );

        $admin = Role::findByName(
            RoleEnum::ADMIN->value,
            'sanctum'
        );

        $author = Role::findByName(
            RoleEnum::AUTHOR->value,
            'sanctum'
        );

        $reader = Role::findByName(
            RoleEnum::READER->value,
            'sanctum'
        );

        // Get all permission names
        $allPermissions = array_map(
            fn(PermissionEnum $permission) => $permission->value,
            PermissionEnum::cases()
        );

        // Super admin gets every permission
        $superAdmin->syncPermissions($allPermissions);

        // Admin manages content and users,
        // but not roles or permissions
        $admin->syncPermissions([
            PermissionEnum::VIEW_POSTS->value,
            PermissionEnum::CREATE_POSTS->value,
            PermissionEnum::EDIT_POSTS->value,
            PermissionEnum::DELETE_POSTS->value,
            PermissionEnum::PUBLISH_POSTS->value,

            PermissionEnum::VIEW_CATEGORIES->value,
            PermissionEnum::CREATE_CATEGORIES->value,
            PermissionEnum::EDIT_CATEGORIES->value,
            PermissionEnum::DELETE_CATEGORIES->value,

            PermissionEnum::VIEW_USERS->value,
            PermissionEnum::MANAGE_USERS->value,
        ]);

        // Authors manage their content.
        // Ownership is enforced separately by a Post Policy.
        $author->syncPermissions([
            PermissionEnum::VIEW_POSTS->value,
            PermissionEnum::CREATE_POSTS->value,
            PermissionEnum::EDIT_POSTS->value,
            PermissionEnum::DELETE_POSTS->value,
            PermissionEnum::VIEW_CATEGORIES->value,
        ]);

        // Readers can view content only
        $reader->syncPermissions([
            PermissionEnum::VIEW_POSTS->value,
            PermissionEnum::VIEW_CATEGORIES->value,
        ]);

        // Clear cache again after updating permissions
        app()[PermissionRegistrar::class]
            ->forgetCachedPermissions();
    }
}
