<?php

namespace App\Http\Controllers;

use App\Models\Save;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $user_id = Auth::id();
        $media_id = $request->target_id;

        $saved = Save::where('user_id', $user_id)->where('media_id', $media_id)->count();
        if($saved > 0) {
            return response()->json(true);
        } else {
            $saved = new Save;
            $saved->user_id = $user_id;
            $saved->media_id = $request->target_id;
            $saved->save();

            return response()->json(true);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Save  $save
     * @return \Illuminate\Http\Response
     */
    public function show(Save $save)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Save  $save
     * @return \Illuminate\Http\Response
     */
    public function edit(Save $save)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Save  $save
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Save $save)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Save  $save
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
        $user_id = Auth::id();
        $media_id = $request->target_id;
        $saved = Save::where('user_id', $user_id)->where('media_id', $media_id)->delete();

        return response()->json(false);
    }
}
