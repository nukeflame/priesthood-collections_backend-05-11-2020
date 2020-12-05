<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'current_password' => 'required',
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required|min:6',
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'email.unique' => 'Your password needs to be at least 8 characters. Please enter a longer one.',
    //         'email.unique' => 'Passwords do not match.',
    //         'email.unique' => 'The password you entered was incorrect.',
    //     ];
    // }
}
