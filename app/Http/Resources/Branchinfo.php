<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Branchinfo extends JsonResource
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
            'phone' => $this->phone,
            'name' => $this->name,
            'open_time'=>$this->open_time,
            'closed_time'=>$this->closed_time,
            'address'=>$this->address,
        ];
    }
}
