<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAnswers extends Model
{
    public function quizAttempt(){
        return $this->belongsTo(QuizAttempt::class,'attempt_id');
    }
    public function question(){
        return $this->belongsTo(Question::class,'question_id');
    }
    public function answer(){
        return $this->belongsTo(Answer::class,'answer_id');
    }
}
