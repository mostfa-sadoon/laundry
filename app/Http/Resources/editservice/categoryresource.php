<?php

namespace App\Http\Resources\editservice;

use Illuminate\Http\Resources\Json\JsonResource;

class categoryresource extends JsonResource
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
            'category_id' => $this->id,
            'name' => $this->name,
            //'branchitems'=>branchitem::collection($this->branchitems),
        ];
    }
}
