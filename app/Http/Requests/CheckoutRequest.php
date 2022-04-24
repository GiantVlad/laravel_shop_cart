<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
            'product_ids.*' => 'required|integer|min:1|max:99999',
            'subtotal' => 'required',
            'paymentMethodId' => 'required|integer|exists:payment_methods,id,enabled,1',
            'shippingMethodId' => 'required|integer|exists:shipping_methods,id,enable,1',
            'related_product_id' => 'integer|max:99999',
            'isRelatedProduct.*' => 'required|boolean',
            'productQty.*' => 'required|integer|min:1|max:99',
        ];
    }
    
    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'productQty.*.required' => 'The Quantity field can not be blank.',
            'productQty.*.integer' => 'Quantity must be integer.',
            'productQty.*.min' => 'Minimum of Quantity is 1 psc.',
            'productQty.*.max' => 'Maximum of Quantity is 99 psc.'
        ];
    }
}
