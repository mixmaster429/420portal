<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Media;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    // protected function registered(Request $request, User $user)
    // {
    //     if ($user instanceof MustVerifyEmail) {
    //         return response()->json(['status' => trans('verification.sent')]);
    //     }

    //     return response()->json($user);
    // }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'username' => $data['name'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'is_active' => 1,
        ]);
        $user_id = $user->id;

        $media = Media::create([
            'url' => $data['logo'],
            'user_id' => $user_id,
            'model' => 'profile',
            'type' => 'image'
        ]);

        $media_id = $media->id;

        $user->media_id = $media_id;
        $user->save();

        $mdata = array();
        $mdata['user_name'] = $data['name'];
        $toEmail = $data['email'];
        try {
            Mail::to($toEmail)->send(new SendEmail($mdata, $user));
        } catch (\Throwable $th) {
            //throw $th;
        }
        return $user;
    }
}
