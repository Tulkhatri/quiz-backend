<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticatedUserApi
{
    public function handle($request, Closure $next)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                throw new JWTException('Token not provided', Response::HTTP_UNAUTHORIZED);
            }

            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                throw new Exception('User not found.', Response::HTTP_NOT_FOUND);
            }
            
            if ($user->trashed()) {
                throw new Exception('User account is deactivated.', Response::HTTP_FORBIDDEN);
            }
        } 
        catch (TokenExpiredException $e) {
            return $this->errorResponse('Token expired', Response::HTTP_UNAUTHORIZED);
        } 
        catch (TokenInvalidException $e) {
            return $this->errorResponse('Token invalid', Response::HTTP_UNAUTHORIZED);
        } 
        catch (JWTException $e) {
            return $this->errorResponse('Token missing', Response::HTTP_UNAUTHORIZED);
        }
        catch (Exception $e) {
            $statusCode = $this->getValidStatusCode($e);
            return $this->errorResponse($e->getMessage(), $statusCode);
        }
        
        return $next($request);
    }

    protected function errorResponse(string $message, int $statusCode)
    {
        return response()->json([
            'message' => $message,
        ], $statusCode);
    }

    protected function getValidStatusCode(Exception $e): int
    {
        // Default status codes for specific exceptions
        if ($e instanceof JWTException) {
            return Response::HTTP_UNAUTHORIZED;
        }

        // Get code from exception if it's a valid HTTP status code
        $code = $e->getCode();
        if ($code >= 100 && $code < 600) {
            return $code;
        }

        // Fallback status codes based on exception type
        return Response::HTTP_BAD_REQUEST;
    }
}