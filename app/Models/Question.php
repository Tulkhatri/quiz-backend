<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
   public function createdBy(){
    return $this->belongsTo(User::class,'created_by');
   }
   
    public function quiz(){
    return $this->belongsTo(Quiz::class,'quiz_id');
   }

     public static function listData(){
        try{
            $data=Question::with('quiz')->select('id','quiz_id','question_text')
                ->where('status', 'Y')
                ->latest()
                ->get();

            return $data;
        }catch(Exception){
            return collect();
        }
    }
   
}
