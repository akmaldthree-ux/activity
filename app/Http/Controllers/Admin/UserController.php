<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('division')
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when($request->division_id, fn($q) => $q->where('division_id', $request->division_id))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $divisions = Division::orderBy('name')->get();
        return view('admin.users.index', compact('users', 'divisions'));
    }

    public function create()
    {
        $divisions = Division::orderBy('name')->get();
        return view('admin.users.form', compact('divisions') + ['user' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'unique:users'],
            'password'    => ['required', 'string', 'min:8'],
            'role'        => ['required', 'in:karyawan,manager,admin'],
            'division_id' => ['nullable', 'exists:divisions,id'],
        ]);

        $data['password']  = Hash::make($data['password']);
        $data['is_active'] = true;

        User::create($data);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $divisions = Division::orderBy('name')->get();
        return view('admin.users.form', compact('user', 'divisions'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password'    => ['nullable', 'string', 'min:8'],
            'role'        => ['required', 'in:karyawan,manager,admin'],
            'division_id' => ['nullable', 'exists:divisions,id'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User dihapus.');
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', $user->is_active ? 'User diaktifkan.' : 'User dinonaktifkan.');
    }
}
