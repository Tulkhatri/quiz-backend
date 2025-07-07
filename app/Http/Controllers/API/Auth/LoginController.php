<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\AppController;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Common;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Info(
 *    title="Quiz",
 *    version="1.0",
 * )
 */

class LoginController extends AppController
{

    /**
* @OA\Post(
*     path="/api/login",
*     operationId="Login",
*     tags={"Login"},
*     summary="User Login",
*     description="User Login here",
*     @OA\RequestBody(
*         required=true,
*         @OA\MediaType(
*            mediaType="multipart/form-data",
*            @OA\Schema(
*               type="object",
*               @OA\Property(property="email", type="string", example="admin@gmail.com"),
*               @OA\Property(property="password", type="string", example="acpassword"),
*            ),
*        ),
*        @OA\MediaType(
*            mediaType="application/json",
*            @OA\Schema(
*               type="object",
*               required={"email", "password"},
*               @OA\Property(property="email", type="string", example="admin@gmail.com"),
*               @OA\Property(property="password", type="string", example="acpassword"),
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

    public function __construct()
    {
        parent::__construct();
    }
   
        public function login(LoginRequest $request)
            {
                try {
                    $message = 'Login successful';
                    $status=$this->status;
                    $token=null;

                    $post = $request->validated();
                    $credentials = [
                        'email' => $post['email'],
                        'password' => $post['password'],
                    ];

                    if(!Auth::attempt(['email'=>$post['email'],'password'=> $post['password'], 'status'=>'Y'])){
                        throw new Exception('Invalid mobile number or password.');
                    }
                     $token = auth('authapi')->attempt($credentials);


                }  catch (QueryException $e) {
                    $status = 500;
                    $message = $this->queryMessage;
                } catch (Exception $e) {
                    $status = 401;
                    $message = $e->getMessage();
                }
                return Common::getJsonData($status,$message,['token'=>$token]);
            }


            
        public function logout()
        {
            auth('authapi')->logout();

            return response()->json(['message' => 'Successfully logged out']);
        }
     
    }
