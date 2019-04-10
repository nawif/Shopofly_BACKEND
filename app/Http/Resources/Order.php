<?php

namespace App\Http\Resources;
use App\Http\Resources\OrderListing as ListingResource;
use App\Http\Resources\Address as AddressResource;
use App\Http\Resources\Customer as CustomerResource;
use App\Http\Resources\Transaction as TransactionResource;






use Illuminate\Http\Resources\Json\JsonResource;

class Order extends JsonResource
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
            "items" => ListingResource::collection($this->listings, $this->id),
            "total" => $this->getBill(),
            "address" => new AddressResource($this->address),
            "customer" => new CustomerResource($this->user),
            "order_id" => $this->id,
            "transaction" => new TransactionResource($this->transaction),
        ];
    }
}
