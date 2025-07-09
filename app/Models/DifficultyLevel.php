<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class DifficultyLevel extends Model
{
    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }

    public static function listData(){
        try{
            $data=DifficultyLevel::select('id','name')
                ->where('status', 'Y')
                ->latest()
                ->get();

            return $data;
        }catch(Exception){
            return collect();
        }
    }
}
