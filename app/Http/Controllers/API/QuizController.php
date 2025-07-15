<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\AppController;
use App\Http\Requests\QuizRequest;
use App\Models\Common\Common;
use App\Models\Quiz;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizController extends AppController
{

/**
 * @OA\Post(
 *     path="/api/admin/quizzes",
 *     summary="Create a new quiz",
 *     description="Create a new quiz with a title",
 *     operationId="storeQuiz",
 *     tags={"Admin - Quiz"},
 *     security={{ "bearerAuth":{ }}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","category_id","difficulty_level_id","time_limit_minutes"},
 *             @OA\Property(
 *                 property="title",
 *                 type="string",
 *                 maxLength=255,
 *                 example="Quiz Test 1"
 *             ),
 *             @OA\Property(
 *                 property="category_id",
 *                 type="string",
 *                 maxLength=2,
 *                 example="1"
 *             ),
 *             @OA\Property(
 *                 property="difficulty_level_id",
 *                 type="string",
 *                 maxLength=2,
 *                 example="1"
 *             ),
 *             @OA\Property(
 *                 property="time_limit_minutes",
 *                 type="string",
 *                 maxLength=2,
 *                 example="5"
 *             )
 *         )
 *     ),
 *       @OA\Response(
 *          response=200,
 *          description="Quiz created successfully",
 *          @OA\JsonContent()
 *      ),
 *     @OA\Response(
 *         response=201,
 *         description="Quiz created successfully",
 *          @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *     )
 * )
 * 
 * 
 *  @OA\Put(
 *     path="/api/admin/quizzes/{id}",
 *     summary="Update an existing quiz",
 *     description="Update a quiz by ID with a new title",
 *     operationId="updateQuiz",
 *     tags={"Admin - Quiz"},
 *     security={{ "bearerAuth":{ }}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the quiz to update",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","category_id","difficulty_level_id","time_limit_minutes"},
 *             @OA\Property(
 *                 property="title",
 *                 type="string",
 *                 maxLength=255,
 *                 example="Updated quiz title"
 *             ),
 *             @OA\Property(
 *                 property="category_id",
 *                 type="string",
 *                 maxLength=2,
 *                 example="2"
 *             ),
 *             @OA\Property(
 *                 property="difficulty_level_id",
 *                 type="string",
 *                 maxLength=2,
 *                 example="2"
 *             ),
 *             @OA\Property(
 *                 property="time_limit_minutes",
 *                 type="string",
 *                 maxLength=2,
 *                 example="5"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Quiz updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Quiz updated successfully"),
 *             @OA\Property(property="response", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Quiz not found"
 *     )
 * )
 */

 public function save(QuizRequest $request) {
    try{
        $post =$request->validated();
        $userid= Auth::user()->id;
        $post['table'] = 'quizzes';
        $post['id'] = !empty($post['id']) ? $post['id'] : '';

        $insertArray = [
                     'title' => $post['title'],
                     'category_id' => $post['category_id'],
                     'difficulty_level_id' => $post['difficulty_level_id'],
                     'time_limit_minutes' => $post['time_limit_minutes'],
                     ];

            if(!empty($post['id'])){
                $insertArray['updated_by'] = $userid;
                $insertArray['updated_at'] = date('Y-m-d H:i:s');
            } else {
                $insertArray['created_by'] = $userid;
                $insertArray['created_at'] = date('Y-m-d H:i:s');
            }

            $post['insertArray'] = $insertArray;

            DB::beginTransaction();
            $isSaved = Common::saveData($post);
                if (!$isSaved) {
                    throw new Exception ("Couldn't save data. Please, try again.", 1);
                }
                $this->response=true;
                if(empty($post['id'])){
                    $this->message="Data inserted successfully";
                }else{
                    $this->message="Data Updated successfully";
                }

            DB::commit();
    } catch(QueryException $e) {
        DB::rollBack();
        $this->status=500;
        $this->message=$this->queryMessage;
    } catch(Exception $e) {
        DB::rollBack();
        $this->status=301;
        $this->message=$e->getMessage();
    }
    return Common::getJsonData($this->status, $this->message, $this->response);
}

/**
 *  @OA\Get(
 *     path="/api/admin/quizzes",
 *     operationId="getQuizList",
 *     tags={"Admin - Quiz"},
 *     security={{ "bearerAuth":{ }}},
 *     summary="Get a list of active quizzes",
 *     description="Returns a list of quizzes with status 'Y'.",
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Data retrieved successfully."),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="title", type="string", example="Quiz title .."),
 *                     @OA\Property(property="category_id", type="string", example="2"),
 *                     @OA\Property(property="difficulty_level_id", type="string", example="1"),
 *                     @OA\Property(property="time_limit_minutes", type="string", example="5"),
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request or no data found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=400),
 *             @OA\Property(property="message", type="string", example="Couldn't get data. Please, try again."),
 *             @OA\Property(property="data", type="null", example=null)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="Database query error."),
 *             @OA\Property(property="data", type="null", example=null)
 *         )
 *     )
 * )
 * 
 */

public function list() {
    try{
        $mappedData=[];
        $data = Quiz::listData();
            if (!$data) {
                throw new Exception ("Couldn't get data.Please, try again.", 1);
            }
            $this->message="Data listed successfully";

           foreach ($data as $quiz) {
                $mappedData[] = [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'category_id' => $quiz->category->id ?? null,
                    'difficulty_level_id' => $quiz->difficultyLevel->id ?? null,
                    'category_name' => $quiz->category->name ?? null,
                    'difficulty_level_name' => $quiz->difficultyLevel->name ?? null,
                    'time_limit_minutes' => $quiz->time_limit_minutes,
                ];
        }

        $this->response=$mappedData;

    } catch(QueryException $e) {
        $this->status=500;
        $this->message=$this->queryMessage;
    } catch(Exception $e) {
        $this->status=301;
        $this->message=$e->getMessage();
    }
    return Common::getJsonData($this->status, $this->message, $this->response);
}


/**
 *  @OA\Delete(
 *     path="/api/admin/quizzes/{id}",
 *     summary="Delete a quiz",
 *     description="Deletes a quiz by its ID",
 *     operationId="deleteQuiz",
 *     tags={"Admin - Quiz"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the quiz to delete",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Quiz deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Quiz deleted successfully"),
 *             @OA\Property(property="response", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Quiz not found"
 *     )
 * )
 */

public function delete($id){
    try{
            $post=[];
            $post['userid']=Auth::user()->id;
            $post['table'] = 'quizzes';
            $post['deleteid'] = !empty($id) ? $id : '';
          
            DB::beginTransaction();

            $isDelete = Common::deleteData($post);
            if (!$isDelete) {
                throw new Exception ("Couldn't delete data. Please, try again.", 1);
            }
            $this->response=true;
            $this->message="Data deleted successfully";
        
            DB::commit();

    } catch(QueryException $e) {
        DB::rollBack();
        $this->status=500;
        $this->message=$this->queryMessage;
    } catch(Exception $e) {
        DB::rollBack();
        $this->status=301;
        $this->message=$e->getMessage();
    }
    return Common::getJsonData($this->status, $this->message, $this->response);
    }
}