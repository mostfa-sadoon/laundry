<?php

namespace App\Models\laundry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use App\Models\laundryservice\Additionalservice;



class branchitem extends Model implements TranslatableContract
{
    use HasFactory,Translatable;
    protected $table='branchitems';
    public $translatedAttributes = ['name'];
    protected $guarded=[];
    protected $hidden=['pivot'];
    public function aditionalservices(){
        return $this->belongsToMany(Additionalservice::class,'serviceitems_price');
    }
}
