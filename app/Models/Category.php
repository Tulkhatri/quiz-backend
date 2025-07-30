<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{

    
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    //need to right when we use create funciton rather than insert
     //insert doesnot protected data so please use fillable and add only required column here
    protected $fillable=[
        'name',
        'created_by'
    ];

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
