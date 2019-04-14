<?php

namespace App\Http\Resources;
use App\Http\Resources\Supplier as SupplierResource;
use App\Http\Resources\ListingImage as ListingImageResource;

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
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'supplier' => new SupplierResource($this->supplier),
            'image_url' => ListingImageResource::collection($this->images()),
        ];
    }
}
