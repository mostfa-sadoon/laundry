<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDriveryStatus extends Model
{
    use HasFactory;
    protected $table='order_delivery_status';
    protected $guarded=[];
}
