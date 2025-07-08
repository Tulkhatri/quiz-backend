<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
   public function createdBy(){
    return $this->belongsTo(User::class,'created_by');
   }

   public function difficultyLevel(){
    return $this->belongsTo(DifficultyLevel::class,'difficulty_level_id');
   }
   
  }
