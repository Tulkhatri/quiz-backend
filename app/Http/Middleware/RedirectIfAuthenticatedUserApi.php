<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RedirectIfAuthenticatedUserApi
{
    public function handle($request, Closure $next)
    {
        try {
        $token = $request->bearerToken();

        if (!$token) {
            throw new JWTException('Token not provided');
        }

        $user = JWTAuth::parseToken()->authenticate(); 
        if (!$user) {
            throw new Exception('User not found.');
        }
        if ($user->status !== 'Y') {
            throw new Exception('User is inactive.');
        }
            $userId =$user->userid;
            $request->merge([
                'userid' => $userId,
            ]);
        } 
        
        catch (TokenExpiredException $e) {
            return response()->json(['type'=>'error','message' => 'Token expired','status' => 401], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['type'=>'error','message' => 'Token invalid','status' => 401], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['type'=>'error','message' => 'Token missing','status' => 401], 401);
        }
        catch (Exception  $e) {
            return response()->json([
                'type' => 'error',
                'message' => $e->getMessage(),
                'response' => 'false',
                'status' => 401
            ]);
        }
        return $next($request);
    }
}
