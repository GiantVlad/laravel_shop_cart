<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartChangeShippingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'subtotal' => 'required|numeric|min:0',
            'shippingMethodId' => 'required|integer|exists:shipping_methods,id,enable,1',
        ];
    }
}
