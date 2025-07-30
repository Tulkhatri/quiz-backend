<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\AppController;
use App\Http\Requests\RegisterRequest;
use App\Models\Common\Common;
use App\Models\User;
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
*        response=200,
*        description="Login Successfully",
*        @OA\JsonContent(
*           type="object",
*             @OA\Property(property="message", type="string", example="User register successfully"),
*            )
*        ),
*
*      @OA\Response(
 *        response=422,
 *        description="Unprocessable Entity - Validation Error",
 *        @OA\JsonContent(
 *            type="object",
 *            @OA\Property(property="message", type="string", example="The given data was invalid."),
 *            @OA\Property(
 *                property="errors",
 *                type="object",
 *                example={
 *                    "email": {
 *                        "The email has already been taken."
 *                    }
 *                }
 *            )
 *        )
 *    ),
* )
*/
public function register(RegisterRequest $request) {
    try{
        $validatedData =$request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);
      
        DB::beginTransaction();
        if (!User::create($validatedData)) {
            throw new Exception ("Couldn't register data.Please, try again.", 422);
        }
        $this->message="User register successfully";

        DB::commit();
    } catch(QueryException $e) {
        DB::rollBack();
        $this->statusCode=$this->queryStatus;
        $this->message=$this->queryMessage;
    } catch(Exception $e) {
        DB::rollBack();
        $this->statusCode=$e->getCode();
        $this->message=$e->getMessage();
    }
    return Common::getJsonData($this->statusCode, $this->message, $this->response);
        }
     
 }
