<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Fields that are stored as files (uploaded), not plain text.
     */
    private array $imageKeys = ['site_logo', 'site_favicon'];

    /**
     * Fields that are checkboxes (booleans stored as '1'/'0' strings).
     */
    private array $booleanKeys = ['cod_enabled'];

    public function edit()
    {
        $settings = Setting::all()->groupBy('group');

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $allSettings = Setting::all()->keyBy('key');

        $rules = [];
        foreach ($allSettings as $key => $setting) {
            if (in_array($key, $this->imageKeys)) {
                $rules[$key] = ['nullable', 'image', 'max:2048'];
            } elseif (in_array($key, $this->booleanKeys)) {
                $rules[$key] = ['nullable'];
            } elseif ($key === 'contact_email') {
                $rules[$key] = ['nullable', 'email', 'max:255'];
            } else {
                $rules[$key] = ['nullable', 'string', 'max:5000'];
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        foreach ($allSettings as $key => $setting) {
            // Images: only overwrite if a new file was actually uploaded
            if (in_array($key, $this->imageKeys)) {
                if ($request->hasFile($key)) {
                    // Delete old file if it exists, to avoid orphaned storage
                    if ($setting->value) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    $path = $request->file($key)->store('settings', 'public');
                    $setting->update(['value' => $path]);
                }
                continue;
            }

            // Booleans: unchecked checkboxes don't POST, so absence = '0'
            if (in_array($key, $this->booleanKeys)) {
                $setting->update(['value' => $request->has($key) ? '1' : '0']);
                continue;
            }

            // Everything else: plain text/textarea, only update if key was submitted
            if ($request->has($key)) {
                $setting->update(['value' => $request->input($key)]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully.',
        ]);
    }
}