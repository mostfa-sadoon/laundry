<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment_methodTranslation extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $table='payment_method_translations';
}
