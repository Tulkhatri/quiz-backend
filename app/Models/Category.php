<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }

    public static function listData(){
        try{
            $data=Category::select('id','name')
                ->where('status', 'Y')
                ->latest()
                ->get();

            return $data;
        }catch(Exception){
            return collect();
        }
    }
}
