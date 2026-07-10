<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::forUser($request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function unread(Request $request)
    {
        $count = Notification::forUser($request->user()->id)->unread()->count();
        $recent = Notification::forUser($request->user()->id)->unread()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return response()->json([
            'count' => $count,
            'notifications' => $recent,
        ]);
    }

    public function markAsRead(Notification $notification, Request $request)
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(403);
        }

        $notification->markAsRead();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }

    public function markAllAsRead(Request $request)
    {
        Notification::forUser($request->user()->id)->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }

    public static function create(
        int $userId,
        string $type,
        string $title,
        ?string $body = null,
        ?string $icon = null,
        ?string $actionUrl = null,
    ): Notification {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'icon' => $icon,
            'action_url' => $actionUrl,
        ]);
    }
}
