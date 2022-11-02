<?php

namespace App\Models\laundryservice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalCategoryService extends Model
{
    use HasFactory;
    public   $table="additionalservice_category";
    public function additionalservice()
    {
        return $this->belongsTo(Aditionalservice::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
