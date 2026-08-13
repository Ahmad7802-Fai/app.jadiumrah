<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\JamaahPublic;

class EnsureJamaahApproved
{
    public function handle($request, Closure $next)
    {
        $user = auth('jamaah')->user();

        if (!$user) {
            abort(401);
        }

        $jamaah = JamaahPublic::find($user->jamaah_id);

        if (!$jamaah) {
            abort(403, 'Data jamaah tidak ditemukan');
        }

        if ($jamaah->status !== 'approved') {
            abort(403, 'Akun Anda belum diverifikasi');
        }

        return $next($request);
    }
}
