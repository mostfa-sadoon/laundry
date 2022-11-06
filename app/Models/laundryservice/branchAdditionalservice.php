<?php

namespace App\Models\laundryservice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class branchAdditionalservice extends Model
{
    use HasFactory;
    protected $table='branch_additionalservice';
    protected $guarded=[];
    protected $hidden=['branchitem_id','created_at','updated_at','id','branch_id'];
    public function additionalservice()
    {
        return $this->belongsTo(Additionalservice::class)->select('id');
    }
}
