<?php

namespace App\Models\Laundry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchitemTranslation extends Model
{
    use HasFactory;
    protected $table='branchitemtranslations';
    protected $hidden=['translations'];
    protected $guarded=[];
}
