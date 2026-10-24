<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Category\StoreCategoryRequest;
use App\Http\Requests\V1\Category\UpdateCategoryRequest;
use App\Http\Resources\V1\CategoryResource;
use App\Models\Category;
use App\Services\V1\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    // Get All Categories With Pagination
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);

        return CategoryResource::collection(
            $this->categoryService->getPaginate($perPage)
        );
    }

    // Store Category
    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->createCategory(
            $request->validated()
        );

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    // Show Single Category
    public function show(int $id)
    {
        $category = Category::findOrFail($id);

        return new CategoryResource($category);
    }

    // Update Category
    public function update(UpdateCategoryRequest $request, int $id)
    {
        $category = $this->categoryService->updateCategory(
            $id,
            $request->validated()
        );

        return new CategoryResource($category);
    }

    // Delete Category
    public function destroy(int $id)
    {
        $this->categoryService->deleteCategory($id);

        return response()->json([
            'message' => 'Category deleted successfully.',
        ]);
    }

    // Toggle Category Status
    public function categoryStatus(int $id)
    {
        $category = $this->categoryService->categoryStatus($id);

        return new CategoryResource($category);
    }
}
