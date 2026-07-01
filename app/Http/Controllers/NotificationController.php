<?php

namespace App\Http\Controllers;

use App\Models\InAppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = InAppNotification::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(20);

        // Mark all as read
        InAppNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(InAppNotification $notification)
    {
        abort_if($notification->user_id !== Auth::id(), 403);
        $notification->update(['read_at' => now()]);

        return redirect($notification->url ?? route('dashboard'));
    }
}
