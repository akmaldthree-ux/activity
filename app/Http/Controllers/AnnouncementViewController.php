<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class AnnouncementViewController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $announcements = Announcement::with(['author', 'division'])
            ->visibleTo($user)
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('announcements.index', compact('announcements'));
    }

    public function show(Announcement $announcement)
    {
        $user = Auth::user();
        // Pastikan user bisa lihat pengumuman ini
        if ($announcement->division_id && $announcement->division_id !== $user->division_id) {
            abort(403);
        }

        return view('announcements.show', compact('announcement'));
    }
}
