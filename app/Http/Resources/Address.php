<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Address extends JsonResource
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
            'status' => $this->status,
            'city' => $this->city,
            'country' => $this->country,
            'district' => $this->district,
            'street' => $this->street,
            'house_number' => $this->house_number,
        ];
    }
}
