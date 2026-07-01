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
        $announcements = Announcement::with(['author', 'division'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $divisions = Division::orderBy('name')->get();
        return view('admin.announcements.form', compact('divisions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'body'        => ['required', 'string'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['user_id']      = Auth::id();
        $data['published_at'] = $data['published_at'] ?? now();

        Announcement::create($data);

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function edit(Announcement $announcement)
    {
        $divisions = Division::orderBy('name')->get();
        return view('admin.announcements.form', compact('announcement', 'divisions'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'body'        => ['required', 'string'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'published_at' => ['nullable', 'date'],
        ]);

        $announcement->update($data);

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
