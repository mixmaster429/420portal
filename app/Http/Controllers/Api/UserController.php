<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use App\Models\Media;

class UserController extends Controller
{
    public function update(Request $request) {
        $user = User::find($request->get('id'));
        $user->name = str_replace(' ', '', $request->get('name'));
        $user->email = $request->get('email');
        $user->description = $request->get('description');
        $user->is_private = $request->get('is_private') ? 1 : 0;
        if($request->get('image_url') != '') {
            $media = Media::create([
                'model' => 'profile',
                'type' => 'image',
                'user_id' => $user->id,
                'url' => $request->get('image_url'),
            ]);
            $user->media_id = $media->id;
        }
        $user->save();
        return response()->json(['status' => 200, 'data' => $user->load('profilePic')]);
    }
}
