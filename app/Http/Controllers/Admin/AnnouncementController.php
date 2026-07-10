<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $announcements = Announcement::with(['author', 'division'])
            ->when(!$user->isAdmin() && !$user->isDireksi(), fn($q) => $q->where('user_id', $user->id))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $user      = Auth::user();
        $divisions = Division::orderBy('name')->get();
        return view('admin.announcements.form', compact('divisions', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'body'         => ['required', 'string'],
            'division_id'  => ['nullable', 'exists:divisions,id'],
            'published_at' => ['nullable', 'date'],
        ]);

        // Manager hanya bisa buat pengumuman untuk divisinya sendiri
        if ($user->isManager()) {
            $data['division_id'] = $user->division_id;
        }

        $data['user_id']      = $user->id;
        $data['published_at'] = $data['published_at'] ?? now();

        Announcement::create($data);

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function edit(Announcement $announcement)
    {
        $this->authorizeAnnouncement($announcement);

        $user      = Auth::user();
        $divisions = Division::orderBy('name')->get();
        return view('admin.announcements.form', compact('announcement', 'divisions', 'user'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->authorizeAnnouncement($announcement);

        $user = Auth::user();

        $data = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'body'         => ['required', 'string'],
            'division_id'  => ['nullable', 'exists:divisions,id'],
            'published_at' => ['nullable', 'date'],
        ]);

        if ($user->isManager()) {
            $data['division_id'] = $user->division_id;
        }

        $announcement->update($data);

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorizeAnnouncement($announcement);

        $announcement->delete();
        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }

    private function authorizeAnnouncement(Announcement $announcement): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && $announcement->user_id !== $user->id) {
            abort(403);
        }
    }
}
