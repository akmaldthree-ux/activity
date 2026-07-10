<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'name_asc');

        $users = User::with('division')
            ->when($request->search, fn($q) => $q->where(fn($inner) =>
                $inner->where('name', 'like', '%'.$request->search.'%')
                      ->orWhere('email', 'like', '%'.$request->search.'%')
            ))
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when($request->division_id, fn($q) => $q->where('division_id', $request->division_id))
            ->when($sort === 'name_asc',  fn($q) => $q->orderBy('name'))
            ->when($sort === 'name_desc', fn($q) => $q->orderByDesc('name'))
            ->when($sort === 'newest',    fn($q) => $q->orderByDesc('created_at'))
            ->when($sort === 'oldest',    fn($q) => $q->orderBy('created_at'))
            ->paginate(20)
            ->withQueryString();

        $divisions = Division::orderBy('name')->get();
        return view('admin.users.index', compact('users', 'divisions'));
    }

    public function create()
    {
        $divisions  = Division::orderBy('name')->get();
        $supervisors = User::whereIn('role', ['leader', 'manager', 'direksi', 'admin'])->orderBy('name')->get();
        return view('admin.users.form', compact('divisions', 'supervisors') + ['user' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'unique:users'],
            'password'    => ['required', 'string', 'min:8'],
            'role'        => ['required', 'in:karyawan,leader,manager,direksi,admin'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'reports_to'  => ['nullable', 'exists:users,id'],
        ]);

        $data['password']  = Hash::make($data['password']);
        $data['is_active'] = true;

        User::create($data);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $divisions   = Division::orderBy('name')->get();
        $supervisors = User::whereIn('role', ['leader', 'manager', 'direksi', 'admin'])->where('id', '!=', $user->id)->orderBy('name')->get();
        return view('admin.users.form', compact('user', 'divisions', 'supervisors'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password'    => ['nullable', 'string', 'min:8'],
            'role'        => ['required', 'in:karyawan,leader,manager,direksi,admin'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'reports_to'  => ['nullable', 'exists:users,id'],
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

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Tidak ada user yang dipilih.');
        }

        // Jangan izinkan hapus diri sendiri
        $ids = array_filter($ids, fn($id) => (int)$id !== auth()->id());

        $count = User::whereIn('id', $ids)->delete();
        return back()->with('success', "{$count} user berhasil dihapus.");
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', $user->is_active ? 'User diaktifkan.' : 'User dinonaktifkan.');
    }

    public function hierarki(Request $request)
    {
        $divisionId = $request->get('division_id');

        $users = User::whereIn('role', User::MONITORED_ROLES)
            ->where('is_active', true)
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->with(['division', 'supervisor'])
            ->orderBy('name')
            ->get();

        $supervisors = User::whereIn('role', ['leader', 'manager', 'direksi', 'admin'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $divisions = Division::orderBy('name')->get();

        return view('admin.users.hierarki', compact('users', 'supervisors', 'divisions', 'divisionId'));
    }

    public function updateHierarki(Request $request)
    {
        $request->validate([
            'reports_to'   => ['required', 'array'],
            'reports_to.*' => ['nullable', 'exists:users,id'],
        ]);

        $count = 0;
        DB::transaction(function () use ($request, &$count) {
            foreach ($request->input('reports_to', []) as $userId => $supervisorId) {
                $updated = User::where('id', (int) $userId)
                    ->whereIn('role', User::MONITORED_ROLES)
                    ->update(['reports_to' => $supervisorId ?: null]);
                $count += $updated;
            }
        });

        return back()->with('success', "Hierarki {$count} user berhasil diperbarui.");
    }
}
