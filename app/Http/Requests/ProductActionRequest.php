<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductActionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
   public function prepareForValidation(): void{
        $this->merge(
            [
                "sizes" => $this->sizes && is_array($this->sizes) ? $this->sizes : ($this->sizes && !is_array($this->sizes) ? json_decode($this->sizes) : null),

            ]
        );
     }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'required',
            'price' => 'nullable|numeric|min:0',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'sizes' => 'nullable|array', // Ensure sizes is an array and can be empty
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'slug.required' => 'The slug is required and must be unique.',
            'price.required' => 'The price field is required and must be a valid number.',
            'sub_category_id.exists' => 'The selected sub-category is invalid.',
            'sizes.array' => 'The sizes field must be an array.',
        ];
    }
}
