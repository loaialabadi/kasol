<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliLoginRequest extends FormRequest
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
            'phone'=>'required|exists:deliveries,phone',
            'password'=>'required',
            'fcm_token'=>'required',
        ];
    }
    public function attributes()
    {
        return [
            'phone'=>'phone',
            'password'=>'Password',
            'fcm_token'=>'Fcm Token',
        ];
    }
}