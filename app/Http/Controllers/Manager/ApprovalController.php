<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Mail\AccountApprovedMail;
use App\Mail\AccountRejectedMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ApprovalController extends Controller
{
    public function index()
    {
        $manager    = Auth::user();
        $divisionId = $manager->isAdmin() ? null : $manager->division_id;

        $pending = User::where('status', 'pending')
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->with('division')
            ->latest()
            ->get();

        $recent = User::whereIn('status', ['active', 'rejected'])
            ->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
            ->where('role', 'karyawan')
            ->with('division')
            ->latest()
            ->limit(20)
            ->get();

        return view('manager.approval', compact('pending', 'recent'));
    }

    public function approve(User $user)
    {
        $manager = Auth::user();

        if ($manager->isManager() && $user->division_id !== $manager->division_id) {
            abort(403);
        }

        $user->update([
            'status'    => 'active',
            'is_active' => true,
        ]);

        Mail::to($user->email)->queue(new AccountApprovedMail($user));

        return back()->with('success', "Akun {$user->name} berhasil disetujui.");
    }

    public function reject(Request $request, User $user)
    {
        $manager = Auth::user();

        if ($manager->isManager() && $user->division_id !== $manager->division_id) {
            abort(403);
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $user->update([
            'status'           => 'rejected',
            'is_active'        => false,
            'rejection_reason' => $request->rejection_reason,
        ]);

        Mail::to($user->email)->queue(new AccountRejectedMail($user));

        return back()->with('success', "Akun {$user->name} ditolak.");
    }
}
