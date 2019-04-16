<?php

namespace App\Http\Middleware;
namespace App\Http\Middleware;
use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class VerifyJWTToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try{
            $user = JWTAuth::toUser($request->input('token'));
        }catch (JWTException $e) {
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {

//                return response()->json(['token_expired'], $e->getStatusCode());
                return response()->json(['status' => 'error','response_body'=>$e->getStatusCode(),'response_message' => 'Token Expired'],$e->getStatusCode());

            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {

//                return response()->json(['token_invalid'], $e->getStatusCode());

                return response()->json(['status' => 'error','response_body'=>$e->getStatusCode(),'response_message' => 'Invalid Token'],$e->getStatusCode());
            }else{

//                return response()->json(['error'=>'Token is required']);
                return response()->json(['status' => 'error','response_body'=>$e->getStatusCode(),'response_message' => 'Token is required'],$e->getStatusCode());
            }
        }
        return $next($request);
    }
}
