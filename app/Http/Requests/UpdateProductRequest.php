<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|integer|exists:categories,id',
            'slug' => [
                'nullable',
                'string',
                Rule::unique('products', 'slug')->ignore($this->product?->id),
            ],
            'image' => 'sometimes|nullable|string',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|required|string',
        ];
    }
}
