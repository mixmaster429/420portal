<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Setting;

class SettingController extends Controller
{
    public function getDescription(Request $request) {
        $field_name = $request->get('field_name');
        $setting = Setting::first();
        $data = [
            'status' => 200,
            'field_name' => $field_name,
            'description' => $setting[$field_name],
        ];
        return response()->json($data);
    }

    public function updateDescription(Request $request) {
        $field_name = $request->get('field_name');
        $setting = Setting::first();
        $setting->update([
            $field_name => $request->get('description'),
        ]);
        return response()->json(['status' => 200, 'description' => $request->get('description')]);
    }
}
