<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\NotificationFilter;

use Auth;

class NotificationFilterController extends Controller
{
    public function save(Request $request){
        $filter = NotificationFilter::where('user_id', Auth::id())->first();
        if(!$filter) {
            $filter = NotificationFilter::create([
                'user_id' => auth()->id(),
                'value' => 'comment_reply, message, like, follow',
            ]);
        }
        $filter->update([
            'value' => implode(', ', $request->get('value')),
        ]);
        return response()->json($request->get('value'));
    }

    public function get_value(Request $request) {
        $filter = auth()->user()->notification_filter;
        if(!$filter) {
            $filter = NotificationFilter::create([
                'user_id' => Auth::id(),
                'value' => 'comment_reply, message, like, follow',
            ]);
        }
        if($filter->value){
            $value = explode(', ', $filter->value);
        } else {
            $value = [];
        }
        
        return response()->json($value);
    }
}
