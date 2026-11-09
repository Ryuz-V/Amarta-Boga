<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->input('role'));
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $roles = Role::all();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'users' => $users,
                'roles' => $roles
            ]);
        }

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles([$validated['role']]);

        ActivityLog::log(
            'CREATE_USER',
            "Administrator membuat pengguna baru: {$user->name} ({$validated['role']})",
            ['user_id' => $user->id, 'role' => $validated['role']]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Pengguna {$user->name} berhasil ditambahkan!",
                'data' => $user->load('roles')
            ], 201);
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna {$user->name} berhasil ditambahkan!");
    }

    public function show(Request $request, User $user)
    {
        $user->load('roles');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        }

        return redirect()->route('admin.users.edit', $user);
    }

    public function edit(User $user): View
    {
        $roles = Role::all();
        $activityLogs = ActivityLog::where('user_id', $user->id)->latest()->take(5)->get();

        return view('admin.users.edit', compact('user', 'roles', 'activityLogs'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);
        $user->syncRoles([$validated['role']]);

        ActivityLog::log(
            'UPDATE_USER',
            "Administrator memperbarui data pengguna: {$user->name}",
            ['user_id' => $user->id, 'role' => $validated['role']]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data pengguna {$user->name} berhasil diperbarui!",
                'data' => $user->load('roles')
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui!");
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat menghapus akun Anda sendiri.'
                ], 422);
            }
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        ActivityLog::log(
            'DELETE_USER',
            "Administrator menghapus akun pengguna: {$userName}",
            ['deleted_user_id' => $user->id]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Pengguna {$userName} berhasil dihapus."
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Pengguna {$userName} berhasil dihapus.");
    }

    public function toggleStatus(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.'
                ], 422);
            }
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::log(
            'TOGGLE_USER_STATUS',
            "Status akun {$user->name} {$statusText}",
            ['user_id' => $user->id, 'is_active' => $user->is_active]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $user->is_active,
                'message' => "Akun {$user->name} berhasil {$statusText}."
            ]);
        }

        return back()->with('success', "Akun {$user->name} berhasil {$statusText}.");
    }
}
