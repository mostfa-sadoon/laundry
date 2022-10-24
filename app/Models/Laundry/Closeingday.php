<?php

namespace App\Models\laundry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Closeingday extends Model   implements TranslatableContract
{
    use HasFactory;
    use HasFactory,Translatable;
    protected $guarded=[];
    protected $table="closeingdaies";
    public $translatedAttributes = ['name'];
}
