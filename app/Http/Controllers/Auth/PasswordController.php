<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Hash;
use Validator;
use Auth;
use Carbon\Carbon;
use Mail;
use App\Mail\ResetPasswordMail;

use App\User;

class PasswordController extends Controller
{
    public function validatePasswordRequest(Request $request) {
        //You can add validation login here
        $user = DB::table('users')->where('email', $request->get('email'))->orWhere('username', $request->get('email'))->first();
        //Check if the user exists
        if (!$user) {
            $data = [
                'status' => 'error',
                'message' => 'These credentials do not match our records.',
            ];
            return response()->json($data);
        }
        DB::table('password_resets')->where('email', $user->email)->delete();
        //Create Password Reset Token
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => str_replace('/', '', Hash::make(now())),
            'created_at' => Carbon::now()
        ]);
        //Get the token just created above
        $tokenData = DB::table('password_resets')->where('email', $user->email)->first();

        if ($this->sendResetEmail($request->email, $tokenData->token)) {
            $data = [
                'status' => 'success',
                'message' => 'Check your email to reset your password.',
            ];
        } else {
            $data = [
                'status' => 'error',
                'message' => 'Something went wrong',
            ];
        }
        return response()->json($data);
    }

    private function sendResetEmail($email, $token)
    {
        //Retrieve the user from the database
        $user = User::where('email', $email)->orWhere('username', $email)->first();
        //Generate, the password reset link. The token generated is embedded in the link
        $link = env('CLIENT_URL') . '/password/reset/' . $token . '?email=' . urlencode($user->email);

        try {
            //Here send the link with CURL with an external email API 
            Mail::to($user->email)->send(new ResetPasswordMail($user, $link));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function resetPassword(Request $request) {
        //Validate input
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $password = $request->password;
        // Validate the token
        $tokenData = DB::table('password_resets')->where('token', $request->token)->first();
        // Redirect the user back to the password reset request form if the token is invalid
        if (!$tokenData) return response()->json(['status' => 'error', 'messages' => ['email' => 'Invalid request token, Try again']]);

        $user = User::where('email', $tokenData->email)->first();
        // Redirect the user back if the email is invalid
        if (!$user) return response()->json(['status' => 'error', 'messages' => ['email' => 'Email not found']]);
        //Hash and update the new password
        $user->password = Hash::make($password);
        $user->save();

        //Delete the token
        DB::table('password_resets')->where('email', $user->email)->delete();
        return response()->json(['status' => 'success', 'user' => $user]);
    }
}
