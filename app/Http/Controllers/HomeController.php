<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

use Auth;
use Mail;
use App\Mail\ContactUsMail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index(Request $request)
    {
        $device_info = is_Mobile();

        if ($device_info) {
            return view('mobile.homepage');
        }

        return view('desktop.homepage');
    }

    public function contact_us()
    {
        return view('contact_us');
    }

    public function mobileRedirect()
    {
        return view('mobile.homepage');
    }

    public function send_contact_message(Request $request) {
        $message =  $request->description;
        $data = array([
            'message' => $message,
        ]);
        try {
            Mail::to('420portal@gmail.com')->send(new ContactUsMail($data));
        } catch (\Throwable $th) {
            //throw $th;
        }
        return back()->with('success', 'Message Sent');
    }
}
