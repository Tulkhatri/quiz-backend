<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function question(){
        return $this->belongsTo(Question::class,'question_id');
    }
}
