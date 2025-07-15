<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\AnswerRequest;
use App\Models\Answer;
use App\Models\Common\Common;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnswerController extends AppController
{
    
/**
 * @OA\Post(
 *     path="/api/admin/answers",
 *     summary="Create a new answers",
 *     description="Create a new answers with a title",
 *     operationId="storeAnswers",
 *     tags={"Admin - Answers"},
 *     security={{ "bearerAuth":{ }}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"question_id","answer_text","is_correct"},
 *             @OA\Property(
 *                 property="question_id",
 *                 type="string",
 *                 maxLength=2,
 *                 example="1"
 *             ),
 *             @OA\Property(
 *                 property="answer_text",
 *                 type="string",
 *                 maxLength=2,
 *                 example="Update with new answer"
 *             ),
 *             @OA\Property(
 *                 property="is_correct",
 *                 type="string",
 *                 maxLength=1,
 *                 example="Y/N"
 *             )
 *         )
 *     ),
 *       @OA\Response(
 *          response=200,
 *          description="Answer created successfully",
 *          @OA\JsonContent()
 *      ),
 *     @OA\Response(
 *         response=201,
 *         description="Answer created successfully",
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
 *     path="/api/admin/answers/{id}",
 *     summary="Update an existing Answer",
 *     description="Update a Answer by ID with a new title",
 *     operationId="updateAnswer",
 *     tags={"Admin - Answers"},
 *     security={{ "bearerAuth":{ }}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the Answers to update",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"question_id","answer_text","is_correct"},
 *             @OA\Property(
 *                 property="question_id",
 *                 type="string",
 *                 maxLength=2,
 *                 example="2"
 *             ),
 *             @OA\Property(
 *                 property="answer_text",
 *                 type="string",
 *                 maxLength=2,
 *                 example="Write Answer"
 *             ),
 *             @OA\Property(
 *                 property="is_correct",
 *                 type="string",
 *                 maxLength=1,
 *                 example="Y/N"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Answer updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Answer updated successfully"),
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
 *         description="Answer not found"
 *     )
 * )
 */

 public function save(AnswerRequest $request,$id = null) {
    try{
        $post =$request->validated();
        $userid= Auth::user()->id;
        $post['table'] = 'answers';
        $post['id'] = !empty($post['id']) ?$post['id'] : '';

        $insertArray = [
                     'question_id' => $post['question_id'],
                     'answer_text' => $post['answer_text'],
                     'is_correct' => $post['is_correct'],
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
 *     path="/api/admin/answers",
 *     operationId="getAnswerList",
 *     tags={"Admin - Answers"},
 *     security={{ "bearerAuth":{ }}},
 *     summary="Get a list of active answers",
 *     description="Returns a list of answers with status 'Y'.",
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
 *                     @OA\Property(property="question_id", type="string", example="1"),
 *                     @OA\Property(property="answer_text", type="string", example="Write a answer"),
 *                     @OA\Property(property="is_correct", type="string", example="Y")
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
        $data = Answer::listData();
            if (!$data) {
                throw new Exception ("Couldn't get data.Please, try again.", 1);
            }
            $this->message="Data listed successfully";

           foreach ($data as $answer) {
                $mappedData[] = [
                    'id' => $answer->id,
                    'question_id' => $answer->question->id ?? null,
                    'question' => $answer->question->question_text ?? null,
                    'answer_text' => $answer->answer_text,
                    'is_correct' => $answer->is_correct,
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
 *     path="/api/admin/answers/{id}",
 *     summary="Delete a answer",
 *     description="Deletes a answer by its ID",
 *     operationId="deleteAnswers",
 *     tags={"Admin - Answers"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the answer to delete",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Answer deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Answer deleted successfully"),
 *             @OA\Property(property="response", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Answer not found"
 *     )
 * )
 */

public function delete($id){
    try{
            $post=[];
            $post['userid']=Auth::user()->id;
            $post['table'] = 'answers';
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