<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $order_label
 * @property string $status
 * @property float $total
 * @property Carbon $created_at
 */
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
