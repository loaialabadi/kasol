<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
       protected function prepareForValidation()
    {
        if ($this->has('sizes') && is_string($this->sizes)) {
            $this->merge([
                'sizes' => json_decode($this->sizes, true) ?? [],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable',
            'slug' => 'nullable',
            'description' => 'nullable',
            'image' => 'nullable',
            'price' => 'nullable',
            'sub_category_id' => 'nullable',
                        'sizes' => 'nullable|array', // Ensure sizes is an array and can be empty

        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'slug.required' => 'The slug is required and must be unique.',
            'price.required' => 'The price field is required and must be a valid number.',
            'sub_category_id.exists' => 'The selected sub-category is invalid.'
        ];
    }
}