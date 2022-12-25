<?php

namespace App\Repositories;
use App\Interfaces\BranchRepositoryInterface;
use App\Models\Laundry\branch;

class BranchRepository implements BranchRepositoryInterface
{
    public function getopentime($branchid){
        $opentime=branch::select('open_time','closed_time')->find($branchid);
        if($opentime==null)
        return false;
        return $opentime;
    }
}
