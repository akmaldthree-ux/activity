<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
        }

        $user = Auth::user();

        if ($user->isPending()) {
            Auth::logout();
            return back()->withErrors(['email' => 'Akun Anda masih menunggu persetujuan dari manager.'])->withInput();
        }

        if ($user->isManagerApproved()) {
            Auth::logout();
            return back()->withErrors(['email' => 'Akun Anda sudah disetujui manager dan menunggu persetujuan akhir dari admin.'])->withInput();
        }

        if ($user->isRejected()) {
            Auth::logout();
            $msg = 'Akun Anda ditolak.';
            if ($user->rejection_reason) {
                $msg .= ' Alasan: ' . $user->rejection_reason;
            }
            return back()->withErrors(['email' => $msg])->withInput();
        }

        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Akun Anda tidak aktif. Hubungi admin.'])->withInput();
        }

        $request->session()->regenerate();
        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
