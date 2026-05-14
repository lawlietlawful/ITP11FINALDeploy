<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);
        
        foreach ($data as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                // If it's a boolean type and value is passed, it means it's checked
                if ($setting->type === 'boolean') {
                    $setting->value = 'true';
                    $setting->save();
                } else {
                    $setting->value = $value;
                    $setting->save();
                }
            }
        }

        // Handle unchecked booleans (they aren't sent in the request)
        $booleanSettings = Setting::where('type', 'boolean')->get();
        foreach ($booleanSettings as $setting) {
            if (!isset($data[$setting->key])) {
                $setting->value = 'false';
                $setting->save();
            }
        }

        return back()->with('success', 'System settings updated successfully.');
    }
}
