<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartChangePaymentRequest extends FormRequest
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
            'paymentMethodId' => 'required|integer|exists:payment_methods,id,enabled,1',
        ];
    }
}
