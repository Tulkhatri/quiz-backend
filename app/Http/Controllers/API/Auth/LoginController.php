<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\AppController;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Common\Common;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Info(
 *    title="Quiz",
 *    version="1.0",
 * ),
 *  security={{ "bearerAuth": {} }}
 */

class LoginController extends AppController
{

/**
* @OA\Post(
*     path="/api/login",
*     operationId="Login",
*     tags={"Auth"},
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
 *     response=200,
 *     description="Login Successful",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Login successful"
 *             ),
 *             @OA\Property(
 *                 property="response",
 *                 type="object",
 *                 @OA\Property(
 *                     property="accessToken",
 *                     type="string",
 *                     example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c"
 *                 )
 *             )
 *         )
 *     )
 * ),
 * 
*  @OA\Response(
*     response=422,
*     description="Validation Error",
*     @OA\JsonContent(
*         type="object",
*         properties={
*             @OA\Property(property="message", type="string", example="The given data was invalid."),
*             @OA\Property(property="errors", type="object", example={
*                 "email": {"The email field is required."},
*                 "password": {"The password must be at least 8 characters."}
*             })
*         }
*      )
*    )
*  )
*/
    public function __construct()
    {
        parent::__construct();
    }
   
        public function login(LoginRequest $request)
            {
                try {
                    $this->message = 'Login successful';

                    $credentials = $request->validated();

                    $user = User::withTrashed()
                        ->where('email', $credentials['email'])
                        ->first();

                    if ($user && $user->trashed()) {
                        throw new Exception('Your account has been deactivated. Please contact support.',401);
                    }
                 
                    if (!Auth::attempt($credentials)) {
                        throw new Exception('Invalid email or password.',422);
                    }

                    $this->response['accessToken'] = auth('authapi')->attempt($credentials);
                    
                }  catch (QueryException $e) {
                    $this->statusCode = $this->queryStatus;
                    $this->message = $this->queryMessage;
                } catch (Exception $e) {
                    $this->statusCode = $e->getCode();
                    $this->message = $e->getMessage();
                }
                return Common::getJsonData($this->statusCode,$this->message,$this->response);
            }

            
        public function logout()
        {
            auth('authapi')->logout();
            return Common::getJsonData(200,'Successfully logged out',true);
        }
     
    }
