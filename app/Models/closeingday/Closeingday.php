<?php

namespace App\Models\closeingday;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Closeingday extends Model implements TranslatableContract
{

    use HasFactory,Translatable;
    protected $table='closeingdaies';
    public $translatedAttributes = ['name'];
    protected $hidden = ['pivot','translations','created_at','updated_at'];

    protected $guarded=[];
}
