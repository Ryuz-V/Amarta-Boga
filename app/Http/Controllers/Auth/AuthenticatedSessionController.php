<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Audit log login
        ActivityLog::log(
            'LOGIN',
            'Pengguna ' . $user->name . ' berhasil masuk ke sistem POS.',
            ['role' => $user->getRoleNames()->first() ?? 'none'],
            $user->id
        );

        // Redirect based on role
        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang kembali, Administrator ' . $user->name . '!');
        }

        if ($user->hasRole('koki')) {
            return redirect()->intended(route('kds.index'))
                ->with('success', 'Halo Chef ' . $user->name . '! Kitchen Display System siap.');
        }

        // Staff / Kasir default
        return redirect()->intended(route('pos.index'))
            ->with('success', 'Selamat bertugas, ' . $user->name . '! Sistem kasir siap digunakan.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            ActivityLog::log(
                'LOGOUT',
                'Pengguna ' . $user->name . ' telah keluar dari sistem POS.',
                [],
                $user->id
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Anda telah berhasil keluar dari sistem.');
    }
}
