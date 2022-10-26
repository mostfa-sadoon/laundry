<?php

namespace App\Models\laundry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class branchitem extends Model implements TranslatableContract
{
    use HasFactory,Translatable;
    protected $table='branchitems';
    public $translatedAttributes = ['name'];
    protected $guarded=[];
}
