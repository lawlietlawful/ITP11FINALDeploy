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
        $data = $request->except(['_token', '_method', 'expected_keys', 'section']);
        $expectedKeys = $request->input('expected_keys', []);
        
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

        // Handle file uploads separately since they are in $request->file()
        if ($request->hasFile('system_logo')) {
            $setting = Setting::where('key', 'system_logo')->first();
            if ($setting) {
                $path = $request->file('system_logo')->store('logos', 'public');
                $setting->value = '/storage/' . $path;
                $setting->save();
            }
        }

        // Handle unchecked booleans that were expected in this specific form submission
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $setting = Setting::where('key', $key)->first();
                if ($setting && $setting->type === 'boolean' && !isset($data[$key])) {
                    $setting->value = 'false';
                    $setting->save();
                }
            }
        } else {
            // Fallback for older forms that don't send expected_keys, but scoped to the keys that WERE sent?
            // Actually, if we don't send expected_keys, we shouldn't touch ANY other boolean.
        }

        $section = $request->input('section', 'System');
        return back()->with('success', "{$section} settings updated successfully.");
    }
}
