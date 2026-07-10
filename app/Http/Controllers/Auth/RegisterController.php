<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NewRegistrationMail;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function showForm()
    {
        $divisions = Division::orderBy('name')->get();
        return view('auth.register', compact('divisions'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'division_id'           => ['required_unless:role,direksi', 'nullable', 'exists:divisions,id'],
            'role'                  => ['required', 'in:karyawan,leader,manager,direksi'],
            'jabatan'               => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'role'        => $data['role'],
            'division_id' => $data['division_id'],
            'jabatan'     => $data['jabatan'],
            'status'      => 'pending',
            'is_active'   => false,
        ]);

        // Notif ke admin; tambah manager divisi jika pendaftar adalah karyawan/leader
        $managers = User::where('is_active', true)
            ->where(function ($q) use ($user) {
                $q->where('role', 'admin');
                if (in_array($user->role, ['karyawan', 'leader'])) {
                    $q->orWhere(fn($q2) => $q2->where('role', 'manager')
                                              ->where('division_id', $user->division_id));
                }
            })
            ->get();

        try {
            foreach ($managers as $manager) {
                Mail::to($manager->email)->queue(new NewRegistrationMail($manager, $user));
            }
        } catch (\Exception $e) {
            \Log::warning('Registration notification mail failed: ' . $e->getMessage());
        }

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil! Akun Anda sedang menunggu persetujuan manager. Kami akan mengirim email setelah disetujui.');
    }
}
