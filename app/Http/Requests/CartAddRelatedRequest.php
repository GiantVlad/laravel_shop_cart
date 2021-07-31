<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\Cart\CartPostActions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CartAddRelatedRequest extends FormRequest
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
            'id' => 'required|integer|exists:related_products,id',
        ];
    }
}
