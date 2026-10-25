<?php

namespace App\Models;

use App\Enums\CategoryStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable('name', 'slug', 'description', 'status')]
class Category extends Model
{
    protected function casts(): array
    {
        return [
            'status' => CategoryStatus::class,
        ];
    }
}
