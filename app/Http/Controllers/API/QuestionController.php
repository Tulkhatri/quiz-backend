<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\AppController;
use App\Http\Requests\QuestionRequest;
use App\Models\Common\Common;
use App\Models\Question;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionController extends AppController
{
   
/**
 * @OA\Post(
 *     path="/api/admin/questions",
 *     summary="Create a new questions",
 *     description="Create a new questions with a title",
 *     operationId="storeQuestions",
 *     tags={"Admin - Questions"},
 *     security={{ "bearerAuth":{ }}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"quiz_id","question_text"},
 *             @OA\Property(
 *                 property="quiz_id",
 *                 type="string",
 *                 maxLength=2,
 *                 example="1"
 *             ),
 *             @OA\Property(
 *                 property="question_text",
 *                 type="string",
 *                 maxLength=2,
 *                 example="Update with new question"
 *             )
 *         )
 *     ),
 *       @OA\Response(
 *          response=200,
 *          description="Question created successfully",
 *          @OA\JsonContent()
 *      ),
 *     @OA\Response(
 *         response=201,
 *         description="Question created successfully",
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
 *     path="/api/admin/questions/{id}",
 *     summary="Update an existing question",
 *     description="Update a question by ID with a new title",
 *     operationId="updateQuestion",
 *     tags={"Admin - Questions"},
 *     security={{ "bearerAuth":{ }}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the questions to update",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"quiz_id","question_text"},
 *             @OA\Property(
 *                 property="quiz_id",
 *                 type="string",
 *                 maxLength=2,
 *                 example="2"
 *             ),
 *             @OA\Property(
 *                 property="question_text",
 *                 type="string",
 *                 maxLength=2,
 *                 example="5"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Question updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Question updated successfully"),
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
 *         description="Question not found"
 *     )
 * )
 */

 public function save(QuestionRequest $request,$id = null) {
    try{
        $post =$request->validated();
        $userid= Auth::user()->id;
        $post['table'] = 'questions';
        $post['editid'] = !empty($id) ? $id : '';

        $insertArray = [
                     'quiz_id' => $post['quiz_id'],
                     'question_text' => $post['question_text'],
                     ];

            if(!empty($post['editid'])){
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
                if(empty($post['editid'])){
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
 *     path="/api/admin/questions",
 *     operationId="getQuestionList",
 *     tags={"Admin - Questions"},
 *     security={{ "bearerAuth":{ }}},
 *     summary="Get a list of active questions",
 *     description="Returns a list of questions with status 'Y'.",
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
 *                     @OA\Property(property="quiz_id", type="string", example="1"),
 *                     @OA\Property(property="question_text", type="string", example="Write a question"),
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
        $data = Question::listData();
            if (!$data) {
                throw new Exception ("Couldn't get data.Please, try again.", 1);
            }
            $this->message="Data listed successfully";

           foreach ($data as $question) {
                $mappedData[] = [
                    'id' => $question->id,
                    'quiz_name' => $question->quiz->title ?? null,
                    'question_text' => $question->question_text,
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
 *     path="/api/admin/questions/{id}",
 *     summary="Delete a question",
 *     description="Deletes a question by its ID",
 *     operationId="deleteQuestions",
 *     tags={"Admin - Questions"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the question to delete",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Question deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Question deleted successfully"),
 *             @OA\Property(property="response", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="questions not found"
 *     )
 * )
 */
public function delete($id){
    try{
            $post=[];
            $post['userid']=Auth::user()->id;
            $post['table'] = 'questions';
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