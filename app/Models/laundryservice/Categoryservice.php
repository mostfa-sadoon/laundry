<?php

namespace App\Models\laundryservice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoryservice extends Model
{
    use HasFactory;
    public   $table="categoryservices";
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
