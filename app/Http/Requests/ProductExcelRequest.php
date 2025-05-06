<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductExcelRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file'=>'required|file',
        ];
    }

    public function attributes(){
        return [
            'file'=>__('auth.file')
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}