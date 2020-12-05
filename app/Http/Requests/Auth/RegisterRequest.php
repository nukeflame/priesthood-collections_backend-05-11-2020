<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255|string|unique:users,email,'. $this->email,
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required|min:6',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'Email address already in use',
        ];
    }
}
