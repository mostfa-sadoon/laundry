<?php

namespace App\Models\laundryservice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
class Additionalservice extends Model implements TranslatableContract
{
    use HasFactory,Translatable;
    protected $guarded=[];
    public $translatedAttributes = ['name'];
    protected $table='additionalservices';
    protected $hidden=['pivot','translations'];
    public function categoryaditionalservices()
    {
        return $this->hasMany(AdditionalCategoryService::class);
    }
    public function categories(){
        return $this->belongsToMany(Category::class);
    }

    public function branchadditionalservice()
    {
        return $this->hasMany(branchAdditionalservice::class);
    }

    public function itemprices()
    {
        return $this->hasMany(Serviceitemprice::class);
    }
}
