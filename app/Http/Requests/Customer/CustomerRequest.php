<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
            'firstname' => 'required|min:3',
            'lastname' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $this->id,
            'password' => 'nullable|min:6|max:32',
            'mobileNo'=> 'required|min:9|max:9',
            'otherMobileNo' => 'nullable|min:9|max:9',
            'deliveryAddress' => 'required',
            'stateRegion'=> 'required',
            'city' => 'required',
        ];
    }
}
