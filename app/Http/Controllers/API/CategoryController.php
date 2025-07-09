<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\API\AppController;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\Common\Common;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 *  @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your bearer token in format: Bearer <token>"
 * )
 */

class CategoryController extends AppController
{

/**
 * @OA\Post(
 *     path="/api/admin/categories",
 *     summary="Create a new category",
 *     description="Create a new category with a name",
 *     operationId="storeCategory",
 *     tags={"Admin - Categories"},
 *     security={{ "bearerAuth":{ }}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 maxLength=255,
 *                 example="Category1 Test"
 *             )
 *         )
 *     ),
 *       @OA\Response(
 *          response=200,
 *          description="Category created successfully",
 *          @OA\JsonContent()
 *      ),
 *     @OA\Response(
 *         response=201,
 *         description="Category created successfully",
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
 *     path="/api/admin/categories/{id}",
 *     summary="Update an existing category",
 *     description="Update a category by ID with a new name",
 *     operationId="updateCategory",
 *     tags={"Admin - Categories"},
 *     security={{ "bearerAuth":{ }}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the category to update",
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
 *                 example="Updated Category Name"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Category updated successfully"),
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
 *         description="Category not found"
 *     )
 * )
 */

public function save(CategoryRequest $request,$id = null) {
    try{
        $post =$request->validated();
        $userid= Auth::user()->id;
        $post['table'] = 'categories';
        $post['editid'] = !empty($id) ? $id : '';

        $insertArray = [
                     'name' => $post['name'],
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
                    throw new Exception ("Couldn't save data.Please, try again.", 1);
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
 *     path="/api/admin/categories",
 *     operationId="getCategoryList",
 *     tags={"Admin - Categories"},
 *     security={{ "bearerAuth":{ }}},
 *     summary="Get a list of active categories",
 *     description="Returns a list of categories with status 'Y'.",
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
        $data = Category::listData();
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
 * @OA\Delete(
 *     path="/api/admin/categories/{id}",
 *     summary="Delete a category",
 *     description="Deletes a category by its ID",
 *     operationId="deleteCategory",
 *     tags={"Admin - Categories"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the category to delete",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="Category deleted successfully"),
 *             @OA\Property(property="response", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Category not found"
 *     )
 * )
 */
public function delete($id){
    try{
            $post=[];
            $post['userid']=Auth::user()->id;
            $post['table'] = 'categories';
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