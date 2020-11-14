<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flag;

use Auth;
use Mail;
use App\Mail\FlagMail;

class FlagController extends Controller
{
    public function flag(Request $request) {
        $flag = Flag::create([
            'user_id' => Auth::id(),
            'flaggable_id' => $request->get('flaggable_id'),
            'flaggable_type' => $request->get('flaggable_type'),
        ]);
        try {
            Mail::to('420portal@gmail.com')->send(new FlagMail($flag));
        } catch (\Throwable $th) {
            //throw $th;
        }
        return response()->json(['status' => 'success']);
    }
}
