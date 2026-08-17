<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed'
        ];
    }

    public function messages() : array
    {
        return [
            'name.required' => 'The name field cannot be empty.',
            'name.string' => 'The name should be string only.',
            'name.max' => 'The name must be not longer than 255.',

            'email.required'=>'The email field cannot be empty.',
            'email.string' => 'The email should be string only.',
            'email.email' =>  'Please provide a valid email format.',
            'email.unique' => 'This email address is already registered in our system.',

            'password.required' => 'The password is required.',
            'password.string' => 'The password should be string only.',
            'password.min' => 'The password should have minimum 6 letters',
            'password.confirmed' => 'The password confirmation does not match.'
        ];
    }
}
