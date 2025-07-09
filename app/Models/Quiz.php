<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
   public function createdBy(){
    return $this->belongsTo(User::class,'created_by');
   }

   public function difficultyLevel(){
    return $this->belongsTo(DifficultyLevel::class,'difficulty_level_id');
   }

   public function category(){
    return $this->belongsTo(Category::class,'category_id');
   }
   
   public static function listData(){
        try{
            $data=Quiz::with('category','difficultyLevel')->select('id','title','category_id','difficulty_level_id','time_limit_minutes')
                ->where('status', 'Y')
                ->latest()
                ->get();

            return $data;
        }catch(Exception){
            return collect();
        }
    }
}
