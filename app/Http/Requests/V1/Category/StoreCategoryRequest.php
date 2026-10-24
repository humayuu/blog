<?php

namespace App\Http\Requests\V1\Category;

use App\Enums\CategoryStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:categories,name'],
            'description' => ['required', 'string', 'min:10', 'max:255'],
            'status' => ['sometimes', 'string', Rule::enum(CategoryStatus::class)],
        ];
    }
}
