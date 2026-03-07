<?php

namespace App\Http\Resources;

use App\ShippingMethod;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 */
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
        $className = $this->whenLoaded('method')?->class_name;

        return [
            'id' => $this->id,
            'label' => $className ? ShippingMethod::getLabel($className) : '',
        ];
    }
}
