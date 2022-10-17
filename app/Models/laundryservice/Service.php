<?php

namespace App\Models\laundryservice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Service extends Model implements TranslatableContract
{
    use HasFactory,Translatable;
    protected $guarded=[];
    public $translatedAttributes = ['name'];
    protected $hidden = ['pivot'];
    public function categoryservices()
    {
        return $this->hasMany(Categoryservice::class);
    }
    public function categories(){
        return $this->belongsToMany(Category::class);
    }
}
