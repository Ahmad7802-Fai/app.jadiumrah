<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use App\Models\JamaahNotification;

class NotificationController extends Controller
{
    /**
     * LIST NOTIFIKASI JAMAAH
     */
    public function index()
    {
        $jamaah = auth('jamaah')->user()->jamaah;

        // 🔥 AUTO MARK AS READ
        JamaahNotification::where('jamaah_id', $jamaah->id)
            ->where('is_read', 0)
            ->update([
                'is_read' => 1
            ]);

        // ambil notifikasi terbaru
        $notifs = JamaahNotification::where('jamaah_id', $jamaah->id)
            ->latest()
            ->get();

        return view('jamaah.notifications.index', compact(
            'jamaah',
            'notifs'
        ));
    }


    /**
     * DETAIL NOTIFIKASI
     * + AUTO MARK AS READ
     */
    public function show(int $id)
    {
        $jamaahId = auth('jamaah')->user()->jamaah_id;

        $notif = JamaahNotification::where('id', $id)
            ->where('jamaah_id', $jamaahId)
            ->firstOrFail();

        if ($notif->is_read == 0) {
            $notif->update([
                'is_read' => 1,
                'read_at' => now(),
            ]);
        }

        return view('jamaah.notifications.show', compact('notif'));
    }

    /**
     * MARK SINGLE AS READ (OPSIONAL)
     */
    public function markAsRead(int $id)
    {
        $jamaahId = auth('jamaah')->user()->jamaah_id;

        JamaahNotification::where('id', $id)
            ->where('jamaah_id', $jamaahId)
            ->update([
                'is_read' => 1,
                'read_at' => now(),
            ]);

        return back();
    }

    /**
     * MARK ALL AS READ
     */
    public function markAllAsRead()
    {
        $jamaahId = auth('jamaah')->user()->jamaah_id;

        JamaahNotification::where('jamaah_id', $jamaahId)
            ->where('is_read', 0)
            ->update([
                'is_read' => 1,
                'read_at' => now(),
            ]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /**
     * UNREAD COUNT (BADGE)
     */
    public function unreadCount()
    {
        return JamaahNotification::where('jamaah_id', auth('jamaah')->user()->jamaah_id)
            ->where('is_read', 0)
            ->count();
    }
}
