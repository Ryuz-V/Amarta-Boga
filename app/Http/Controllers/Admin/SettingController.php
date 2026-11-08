<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(Request $request): View
    {
        $settings = Setting::all()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $settingsData = $request->input('settings', []);

        foreach ($settingsData as $key => $value) {
            Setting::set($key, $value);
        }

        ActivityLog::log(
            'UPDATE_SETTINGS',
            "Administrator memperbarui pengaturan toko dan konfigurasi struk thermal."
        );

        return back()->with('success', 'Konfigurasi restoran & pengaturan struk berhasil disimpan!');
    }
}
