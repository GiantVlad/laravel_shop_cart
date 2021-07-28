<?php

namespace App\Http\Requests;

use App\Services\Order\OrderActions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderActionRequest extends FormRequest
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
            'id' => 'required|exists:orders,id',
            'action' => ['required', Rule::in(OrderActions::getActions())]
        ];
    }
}
