<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adress extends Model
{
    use HasFactory;
    public $table='useradress';
    protected $guarded=[];
    public function getCityAttribute($value)
    {
         if($value==null)
         return 'not avilable yet';
         return $value;
    }
    public function getDistrictAttribute($value)
    {
         if($value==null)
         return 'not avilable yet';
         return $value;
    }
    public function getBuildingAttribute($value)
    {
         if($value==null)
         return 'not avilable yet';
         return $value;
    }
    public function getStreetAttribute($value)
    {
         if($value==null)
         return 'not avilable yet';
         return $value;
    }
    public function getFloorAttribute($value)
    {
         if($value==null)
         return 'not avilable yet';
         return $value;
    }
    public function getFlatAttribute($value)
    {
         if($value==null)
         return 'not avilable yet';
         return $value;
    }
    public function getLatAttribute($value)
    {
         if($value==null)
         return 'not avilable yet';
         return $value;
    }
    public function getLongAttribute($value)
    {
         if($value==null)
         return 'not avilable yet';
         return $value;
    }
}
