<?php

namespace App\Models\Laundry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\closeingday\Closeingday;

// use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
// use Astrotomic\Translatable\Translatable;

class branch extends Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $guarded=[];
    protected $table="branchs";
    public $translatedAttributes = ['name'];
    public function closingdayes(){
        return $this->belongsToMany(Closeingday::class,'branch_closingdaies');
    }
    public function branchservices(){
        return $this->hasMany(branchservice::class,'branch_id');
    }

    public function laundry(){
        return $this->belongsTo(Laundry::class,'laundry_id');
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
