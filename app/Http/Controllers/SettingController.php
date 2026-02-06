<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\ActivityLog;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $settings = Setting::all()->groupBy('group');

        return view('settings.index', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings'        => 'required|array',
            'settings.*.key'  => 'required|string',
            'settings.*.value' => 'nullable|string',
        ]);

        $updatedKeys = [];

        foreach ($validated['settings'] as $setting) {
            $existing = Setting::where('key', $setting['key'])->first();

            if ($existing) {
                $oldValue = $existing->value;
                $existing->update(['value' => $setting['value']]);

                if ($oldValue !== $setting['value']) {
                    $updatedKeys[] = $setting['key'];
                }
            } else {
                Setting::create([
                    'key'   => $setting['key'],
                    'value' => $setting['value'],
                    'group' => 'general',
                ]);
                $updatedKeys[] = $setting['key'];
            }
        }

        if (count($updatedKeys) > 0) {
            ActivityLog::create([
                'user_id'    => auth()->id(),
                'action'     => 'Update Settings',
                'module'     => 'Settings',
                'details'    => "Updated settings: " . implode(', ', $updatedKeys) . ".",
                'ip_address' => $request->ip(),
            ]);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Settings have been updated successfully.');
    }
}
