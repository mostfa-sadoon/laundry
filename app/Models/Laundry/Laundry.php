<?php

namespace App\Models\Laundry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Laundry extends Authenticatable implements JWTSubject,TranslatableContract
{
    use HasFactory,Translatable;
    protected $guarded=[];
    protected $table="laundries";
    public $translatedAttributes = ['name'];

    public function branchitem()
    {
        return $this->hasMany(Categoryservice::class);
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
