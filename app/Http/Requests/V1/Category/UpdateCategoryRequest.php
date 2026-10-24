<?php

namespace App\Http\Requests\V1\Category;

use App\Enums\CategoryStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $category = $this->route('category') ?? $this->route('id');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('categories', 'name')->ignore($category),
            ],
            'description' => [
                'sometimes',
                'required',
                'string',
                'min:10',
                'max:255',
            ],
            'status' => [
                'sometimes',
                'required',
                'string',
                Rule::enum(CategoryStatus::class),
            ],
        ];
    }
}
