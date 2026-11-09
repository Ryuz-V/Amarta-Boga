<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $settings = Setting::all()->groupBy('group');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'settings' => $settings
            ]);
        }

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settingsData = $request->input('settings', []);

        foreach ($settingsData as $key => $value) {
            Setting::set($key, $value);
        }

        ActivityLog::log(
            'UPDATE_SETTINGS',
            "Administrator memperbarui pengaturan toko dan konfigurasi struk thermal."
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Konfigurasi restoran & pengaturan struk berhasil disimpan!',
                'data' => Setting::pluck('value', 'key')
            ]);
        }

        return back()->with('success', 'Konfigurasi restoran & pengaturan struk berhasil disimpan!');
    }
}
