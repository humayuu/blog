<?php

namespace App\Services\V1;

use App\Enums\CategoryStatus;
use App\Models\Category;

class CategoryService
{
    // Get All Categories With Pagination
    public function getPaginate(int $perPage = 15)
    {
        $perPage = max(1, min($perPage, 100));

        return Category::orderBy('name')->paginate($perPage);
    }

    // Create Category
    public function createCategory(array $data): Category
    {
        return Category::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'status' => $data['status'] ?? CategoryStatus::ACTIVE->value,
        ]);
    }

    // Update Category
    public function updateCategory(int $id, array $data): Category
    {
        $category = Category::findOrFail($id);

        $category->update([
            'name' => $data['name'] ?? $category->name,
            'description' => $data['description'] ?? $category->description,
            'status' => $data['status'] ?? $category->status,
        ]);

        return $category->refresh();
    }

    // Delete Category
    public function deleteCategory(int $id): bool
    {
        $category = Category::findOrFail($id);

        return (bool) $category->delete();
    }

    // Toggle Category Status
    public function categoryStatus(int $id): Category
    {
        $category = Category::findOrFail($id);

        $category->status = (
            $category->status === CategoryStatus::ACTIVE->value
        )
            ? CategoryStatus::INACTIVE->value
            : CategoryStatus::ACTIVE->value;

        $category->save();

        return $category->refresh();
    }
}
