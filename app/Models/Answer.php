<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function question(){
        return $this->belongsTo(Question::class,'question_id');
    }

    public static function listData(){
        try{
            $data=Answer::with('question')->select('id','question_id','answer_text','is_correct')
                ->where('status', 'Y')
                ->latest()
                ->get();

            return $data;
        }catch(Exception){
            return collect();
        }
    }
}
