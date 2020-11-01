<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Notification;
use App\User;

use Auth;

class NotificationController extends Controller
{    
    public function index(Request $request) {
        return view('notification.index');
    }
    
    public function user_unreads(Request $request) {
        $unread = Notification::where('user_id', $request->get('id'))->whereRead(0)->where( 'media_type', '!=', 'portal')->count();
        return response()->json($unread);
    }

    public function get_all(Request $request) {
        $user = auth()->user();
        $per_page = 15;
        $data = $user->notifications()->where( 'media_type', '!=', 'portal')->orderBy('created_at', 'desc')->paginate($per_page);
        foreach ($data as $item) {
            $item->read = 1;
            $item->save();
        }
        return response()->json($data);
    }
}
