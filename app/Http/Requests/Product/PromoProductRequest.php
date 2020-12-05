<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class PromoProductRequest extends FormRequest
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
            'infoName' => 'required',
            'infoPrice' => 'required',
            'infoTags' => 'required',
            'pInfo' => 'required',
            'products' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'infoName.required' => 'This field is required.',
            'infoPrice.required' => 'This field is required.',
            'infoTags.required' => 'This field is required.',
            'pInfo.required' => 'This field is required.',
            'products.required' => 'Select products for the promo.',
        ];
    }
}
