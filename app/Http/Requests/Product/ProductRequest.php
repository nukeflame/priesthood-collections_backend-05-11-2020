<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'productName' => 'required|min:3',
            'price' => 'required',
            'sku' => 'nullable',
            'category' => 'required|integer',
            'brandName' => 'required',
            'stock' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'price.required' => 'This field is required.',
            'stock.required' => 'This field is required.',
            'category.required' => 'Choose the product category.',
        ];
    }
}
