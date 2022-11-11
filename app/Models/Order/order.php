<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function orderdetailes(){
      return  $this->hasMany(orderdetailes::class);
    }
}
