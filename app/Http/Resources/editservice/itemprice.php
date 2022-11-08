<?php

namespace App\Http\Resources\editservice;

use Illuminate\Http\Resources\Json\JsonResource;

class itemprice extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

         if($this->service_id!=null){
            return [
                'item_price_id' => $this->id,
                'service_id'=>$this->service_id,
                'price' => $this->price,
                ];
         }
         if($this->additionalservice_id!=null){
            return [
                'item_price_id' => $this->id,
                'additionalservice_id'=>$this->additionalservice_id,
                'price' => $this->price,
                ];
         }

    }
}
