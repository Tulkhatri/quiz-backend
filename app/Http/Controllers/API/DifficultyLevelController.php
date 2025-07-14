<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\AppController;
use App\Http\Requests\DifficultyLevelRequest;
use App\Models\Common\Common;
use App\Models\DifficultyLevel;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DifficultyLevelController extends AppController
{
   
/**
 * @OA\Post(
 *     path="/api/admin/difficulty-levels",
 *     summary="Create a new difficulty-levels",
 *     description="Create a new difficulty-levels with a name",
 *     operationId="storedifficulty-levels",
 *     tags={"Admin - Difficulty Levels"},
 *     security={{ "bearerAuth":{ }}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 maxLength=255,
 *                 example="difficulty-level1 Test"
 *             )
 *         )
 *     ),
 *       @OA\Response(
 *          response=200,
 *          description="Difficulty levels created successfully",
 *          @OA\JsonContent()
 *      ),
 *     @OA\Response(
 *         response=201,
 *         description="Difficulty levels created successfully",
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
 *     path="/api/admin/difficulty-levels/{id}",
 *     summary="Update an existing difficulty level",
 *     description="Update a difficulty level by ID with a new name",
 *     operationId="updateDifficulty-levels",
 *     tags={"Admin - Difficulty Levels"},
 *     security={{ "bearerAuth":{ }}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the difficulty levels to update",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 maxLength=255,
 *                 example="difficulty-levels Name"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Difficulty levels updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="difficulty-levels"),
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
 *         description="difficulty-levels not found"
 *     )
 * )
 * 
 * 
 */

public function save(DifficultyLevelRequest $request) {
    try{
        $post =$request->validated();
        $userid= Auth::user()->id;
        $post['table'] = 'difficulty_levels';
        $post['id'] = !empty($post['id']) ? $post['id'] : '';

        $insertArray = [
                     'name' => $post['name'],
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
                    throw new Exception ("Couldn't save data.Please, try again.", 1);
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
 *     path="/api/admin/difficulty-levels",
 *     operationId="difficulty-levelsList",
 *     tags={"Admin - Difficulty Levels"},
 *     security={{ "bearerAuth":{ }}},
 *     summary="Get a list of active difficulty-levels",
 *     description="Returns a list of difficulty-levels with status 'Y'.",
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
 *                     @OA\Property(property="name", type="string", example="Electronics")
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
 */
public function list() {
    try{
        $data=[];
        $data = DifficultyLevel::listData();
            if (!$data) {
                throw new Exception ("Couldn't get data.Please, try again.", 1);
            }
            $this->message="Data listed successfully";
            $this->response=$data;

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
 *     path="/api/admin/difficulty-levels/{id}",
 *     summary="Delete a difficulty level",
 *     description="Deletes a difficulty level by its ID",
 *     operationId="deleteDifficulty-levels",
 *     tags={"Admin - Difficulty Levels"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the difficulty-level to delete",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Difficulty-levels deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Difficulty-levels deleted successfully"),
 *             @OA\Property(property="response", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Difficulty-levels not found"
 *     )
 * )
 */
public function delete($id){
    try{
            $post=[];
            $post['userid']=Auth::user()->id;
            $post['table'] = 'difficulty_levels';
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