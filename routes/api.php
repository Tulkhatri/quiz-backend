<?php

use App\Http\Controllers\API\AnswerController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\DifficultyLevelController;
use App\Http\Controllers\API\QuestionController;
use App\Http\Controllers\API\QuizController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

Route::group(['middleware' => 'authapi'], function () {
    
    Route::group(['prefix'=>'admin'],function(){

            Route::group(['prefix'=>'categories'],function(){
                Route::get('/',[CategoryController::class,'list']);
                Route::post('/',[CategoryController::class,'save']);
                Route::put('/{id}',[CategoryController::class,'save']);// update data from same function 
                Route::delete('/{id}',[CategoryController::class,'delete']);
             });


            Route::group(['prefix'=>'difficulty-levels'],function(){
                Route::get('/',[DifficultyLevelController::class,'list']);
                Route::post('/',[DifficultyLevelController::class,'save']);
                Route::put('/{id}',[DifficultyLevelController::class,'save']);// update data from same function 
                Route::delete('/{id}',[DifficultyLevelController::class,'delete']);
             });


            Route::group(['prefix'=>'quizzes'],function(){
                Route::get('/',[QuizController::class,'list']);
                Route::post('/',[QuizController::class,'save']);
                Route::put('/{id}',[QuizController::class,'save']);// update data from same function 
                Route::delete('/{id}',[QuizController::class,'delete']);
             });


            Route::group(['prefix'=>'questions'],function(){
                Route::get('/',[QuestionController::class,'list']);
                Route::post('/',[QuestionController::class,'save']);
                Route::put('/{id}',[QuestionController::class,'save']);// update data from same function 
                Route::delete('/{id}',[QuestionController::class,'delete']);
             });

             
            Route::group(['prefix'=>'answers'],function(){
                Route::get('/',[AnswerController::class,'list']);
                Route::post('/',[AnswerController::class,'save']);
                Route::put('/{id}',[AnswerController::class,'save']);// update data from same function 
                Route::delete('/{id}',[AnswerController::class,'delete']);
             });
             
    });

    Route::post('/logout', [LoginController::class, 'logout']);
});
