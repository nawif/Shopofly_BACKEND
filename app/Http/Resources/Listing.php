<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Listing extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'key' => $this->key,
            'itemName' => $this->itemName,
            'price' => $this->price,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'supplier' => Supplier::collection($this->supplier),
            'image_url' => Supplier::collection($this->images),
        ];
    }
}
