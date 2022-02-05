<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartAddRequest extends FormRequest
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
            'productId' => 'required|integer|min:1|max:99999',
            'productQty' => 'required|integer|min:1|max:99',
        ];
    }
    
    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'productQty.required' => 'The Quantity field can not be blank.',
            'productQty.integer' => 'Quantity must be integer.',
            'productQty.min' => 'Minimum of Quantity is 1 psc.',
            'productQty.max' => 'Maximum of Quantity is 99 psc.'
        ];
    }
}
