<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->order_label,
            'total' => $this->total,
            'created_at' => $this->created_at->toDateTimeString(),
            'status' => $this->status,
            'uri' => route('order', ['id' => $this->id])
        ];
    }
}
