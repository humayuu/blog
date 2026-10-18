<?php

namespace App\Enums;

enum PermissionEnum: string
{
    case VIEW_POSTS = 'view posts';
    case CREATE_POSTS = 'create posts';
    case EDIT_POSTS = 'edit posts';
    case DELETE_POSTS = 'delete posts';
    case PUBLISH_POSTS = 'publish posts';

    case VIEW_CATEGORIES = 'view categories';
    case CREATE_CATEGORIES = 'create categories';
    case EDIT_CATEGORIES = 'edit categories';
    case DELETE_CATEGORIES = 'delete categories';

    case VIEW_USERS = 'view users';
    case MANAGE_USERS = 'manage users';

    case MANAGE_ROLES = 'manage roles';
    case MANAGE_PERMISSIONS = 'manage permissions';
}
