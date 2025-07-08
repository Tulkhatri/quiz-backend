<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
   public function createdBy(){
    return $this->belongsTo(User::class,'created_by');
   }
   
    public function quiz(){
    return $this->belongsTo(Quiz::class,'quiz_id');
   }

   
}
