<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\AppController;
use App\Http\Requests\RegisterRequest;
use App\Models\Common\Common;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends AppController
{

    /**
* @OA\Post(
*     path="/api/register",
*     operationId="Register",
*     tags={"Auth"},
*     summary="User Register",
*     description="User Register here",
*     @OA\RequestBody(
*         required=true,
*         @OA\MediaType(
*            mediaType="multipart/form-data",
*            @OA\Schema(
*               type="object",
*               @OA\Property(property="name", type="string", example="Tul Khatri"),
*               @OA\Property(property="email", type="string", example="tul@gmail.com"),
*               @OA\Property(property="password", type="string", example="******"),
*               @OA\Property(property="password_confirmation", type="string", example="******"),
*            ),
*        ),
*        @OA\MediaType(
*            mediaType="application/json",
*            @OA\Schema(
*               type="object",
*               required={"name","email", "password"},
*               @OA\Property(property="name", type="string", example="Tul khatri"),
*               @OA\Property(property="email", type="string", example="tul@gmail.com"),
*               @OA\Property(property="password", type="string", example="******"),
*               @OA\Property(property="password_confirmation", type="string", example="******"),
*            ),
*        ),
*    ),
*    @OA\Response(
*        response=201,
*        description="Login Successfully",
*        @OA\JsonContent()
*    ),
*    @OA\Response(
*        response=200,
*        description="Login Successfully",
*        @OA\JsonContent()
*    ),
*    @OA\Response(
*        response=422,
*        description="Unprocessable Entity",
*        @OA\JsonContent()
*    ),
*    @OA\Response(response=400, description="Bad request"),
*    @OA\Response(response=404, description="Resource Not Found"),
* )
*/
public function register(RegisterRequest $request) {
    try{
        $post =$request->validated();
        $post['table'] = 'users';
        $post['editid'] = '';

        $insertArray = [
                     'name' => $post['name'],
                     'email' => $post['email'],
                     'password' => Hash::make($post['password']),
                     'created_at' => date('Y-m-d H:i:s')
                     ];

            $post['insertArray'] = $insertArray;
            DB::beginTransaction();
            $isRegistered = Common::saveData($post);
                if (!$isRegistered) {
                    throw new Exception ("Couldn't register data.Please, try again.", 1);
                }
                $this->response=true;
                $this->message="User register successfully";
               

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
