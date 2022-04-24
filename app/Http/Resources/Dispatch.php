<?php

namespace App\Http\Resources;

use App\ShippingMethod;
use Illuminate\Http\Resources\Json\JsonResource;

class Dispatch extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'label' => ShippingMethod::getLabel($this->whenLoaded('method')?->class_name),
        ];
    }
}
