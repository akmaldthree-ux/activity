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
            'division_id'           => ['required', 'exists:divisions,id'],
            'jabatan'               => ['required', 'string', 'max:255'],
            'no_hp'                 => ['required', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'role'        => 'karyawan',
            'division_id' => $data['division_id'],
            'jabatan'     => $data['jabatan'],
            'no_hp'       => $data['no_hp'],
            'status'      => 'pending',
            'is_active'   => false,
        ]);

        // Kirim email notifikasi ke manager divisi & admin
        $managers = User::where(function ($q) use ($user) {
                $q->where('role', 'manager')->where('division_id', $user->division_id);
            })
            ->orWhere('role', 'admin')
            ->where('is_active', true)
            ->get();

        foreach ($managers as $manager) {
            Mail::to($manager->email)->queue(new NewRegistrationMail($manager, $user));
        }

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil! Akun Anda sedang menunggu persetujuan manager. Kami akan mengirim email setelah disetujui.');
    }
}
