<?php

namespace App\Models\laundryservice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Aditionalservice extends Model implements TranslatableContract
{
    use HasFactory,Translatable;
    protected $guarded=[];
    public $translatedAttributes = ['name'];
    protected $table='additionalservices';
}
