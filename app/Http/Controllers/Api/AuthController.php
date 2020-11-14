<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use Auth;
use JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,name',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if($validator->fails()){
            return sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $user = User::create([
            'name' => $input['username'],
            'username' => $input['username'],
            'slug' => str_slug($input['username']),
            'email' => $input['email'],
            'password' => bcrypt($input['password']),
        ]);
        Auth::login($user);
        $success['access_token'] =  $user->createToken('420portal')->accessToken;
        $success['username'] =  $user->name;
        $success['id'] =  $user->id;
        $success['auth_user'] =  $user;

        $mdata = array();
        $mdata['user_name'] = $user->name;
        $toEmail = $user->email;
        try {
            Mail::to($toEmail)->send(new SendEmail($mdata, $user));
        } catch (\Throwable $th) {
            //throw $th;
        }
        return sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $data = $request->json()->all();
        $user = User::where('username', $data['username'])->where('password', $data['password'])->first();
        if($token = JWTAuth::attempt(['username' => $data['username'], 'password' => $data['password']])){
            $user = auth()->user();
            $success['access_token'] =  $token;
            $success['username'] =  $user->username;
            $success['auth_user'] =  $user;

            return sendResponse($success, 'User login successfully.');
        } else {
            return sendError('Unauthorized.', ['error'=>'Unauthorized']);
        }
    }

    public function logout(){
        $success['result'] =  'success';
        Auth::logout();
        return sendResponse($success, 'User logout successfully.');
    }
}
