<?php

namespace App\Models\Laundry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaundryTranslation extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $table="laundry_translations";
}
