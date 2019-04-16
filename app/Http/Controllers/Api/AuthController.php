<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use JWTAuth;

class AuthController extends Controller
{
    // private $user;
    public function __construct()
    {
        $this->middleware('jwt.auth')->except('login','refresh','register');
    }

    /*
     * Login function */
    public function login(Request $request){

        $credentials = $request->only('email','password');
        $token =null;

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error','response_body'=> $validator->errors() ,'error_msg' => 'Validation failed'],422);
        }
        try
        {
            $token = JWTAuth::attempt($credentials);
            if($token)
            {
                $users = User::where('email',$request->get('email'))->first();

            }else{
                return response()->json(['status' => 'error','response_body'=> '' ,'response_message' => 'Invalid Email and Password'],
                    422);
            }

            if(count($users)>0){
                return response()->json(['status' => 'success','response_body'=> $users ,'response_message' =>'logged in successfully','_token'=>$token]);
            }else{
                return response()->json(['status' => 'error','response_body'=> '' ,'response_message' => 'Invalid Email and Password'],422);
            }
        }catch (JWTAuthException $e){
            return response()->json(['status' => 'error','response_body'=> '' ,'response_message' => 'failed_to_create_token'],500);
        }



    }


    /*
     *function to register new user to the application */
    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'password_confirmation'=>'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error','response_body'=> $validator->errors() ,'response_message' => 'Validation failed'],422);
        }

        $check_existing = User::where('email',$request->get('email'))->first();

        if($check_existing){
            return response()->json(['status' => 'error','response_body'=> '' ,'response_message' => 'The email is already registered. '],422);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password'))
        ]);

        return response()->json(['status'=>true,'response_message'=>'User created successfully','response_body'=>$user]);

    }


    /*
     * function for refreshing the token and get new one*/
    public function refresh(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error','response_body'=> $validator->errors() ,'response_message' => 'Old token required'],422);
        }

        try{

            $token =  JWTAuth::refresh($request->input('token'));
            return response()->json(['token' => $token , 'status' => 'success','response_body'=> '' ,'response_message' => 'Token refreshed successfully']);

        }catch (JWTException $e){
            return response()->json(['status' => 'error','response_body'=> '' ,'response_message' => 'Invalid token'],500);
        }
    }

    /*
     * Get authenticated user detail by token*/
    public function getAuthUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error','response_body'=> $validator->errors() ,'response_message' => 'token required'],422);
        }
        try{
            $user = JWTAuth::toUser($request->token);

            return response()->json(['result' => $user]);

        }catch (JWTException $e){
            return response()->json(['status' => 'error','response_body'=> '' ,'response_message' => $e->getMessage().$e->getLine()],500);
        }
    }
}
