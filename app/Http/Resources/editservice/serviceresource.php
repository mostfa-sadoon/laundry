<?php

namespace App\Http\Resources\editservice;

use Illuminate\Http\Resources\Json\JsonResource;

class serviceresource extends JsonResource
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
            'status'=>true,
            'message'=>'get category id',
            'data' => [
            'service_id' => $this->id,
            'name' => $this->name,
            'categories'=>categoryresource::collection($this->categories),
        ]];
    }
}
