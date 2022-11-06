<?php

namespace App\Models\laundryservice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use App\Models\Laundry\Branchitem;

class Category extends Model implements TranslatableContract
{
    use HasFactory,Translatable;
    protected $guarded=[];
    public $translatedAttributes = ['name'];
    protected $hidden = ['pivot','translations','created_at','updated_at'];

    public function categoryservice()
    {
        return $this->hasMany(Categoryservice::class,'category_id');
    }
    public function servic()
    {
        return $this->belongsToMany(Servic::class);
    }

    public function items(){
        return $this->hasMany(Item::class);
    }

    public function categorytranslate(){
        return $this->hasMany(CategoryTranslations::class);
    }

    public function itemprice(){
        return $this->hasMany(Serviceitemprice::class)->select('branchitem_id','price','category_id','service_id');
    }

    public function branchitems(){
        return $this->hasMany(Branchitem::class);
    }
}
