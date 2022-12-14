<?php

namespace App\Models\Laundry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use App\Models\laundryservice\Additionalservice;
use App\Models\laundryservice\branchAdditionalservice;
use App\Models\Laundry\branchitemTranslation;
use App\Models\laundryservice\Serviceitemprice;
class Branchitem extends Model implements TranslatableContract
{
    use HasFactory,Translatable;
    protected $table='branchitems';
    public $translatedAttributes = ['name'];
    protected $guarded=[];
    protected $hidden=['pivot','translations'];
    public function aditionalservices(){
        return $this->belongsToMany(Additionalservice::class,'branch_additionalservice');
    }
    public function itemadditionalservice(){
        return $this->hasMany(branchAdditionalservice::class);
    }
    public function branchitemprice(){
        return $this->hasMany(Serviceitemprice::class);
    }
}
