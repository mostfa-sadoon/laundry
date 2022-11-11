<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\laundryservice\Service;

class orderdetailes extends Model
{
    use HasFactory;
    protected $table='order_detailes';
    protected $guarded=[];
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
