<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
   public function user(){
     return $this->belongsTo(User::class,'user_id');
   }

   public function quiz(){
    return $this->belongsTo(Quiz::class,'quiz_id');
   }
}
