<?php

namespace App\Http\Controllers\Cabang;

use App\Http\Controllers\Controller;
use App\Models\LeadClosing;

class LeadClosingController extends Controller
{
    public function show(LeadClosing $closing)
    {
        $ctx = app('access.context');

        abort_if(
            $closing->lead->branch_id !== $ctx['branch_id'],
            403
        );

        return view('cabang.closing.show', [
            'closing'  => $closing,
            'lead'     => $closing->lead,
            'readonly' => true, // 🔥 KUNCI UTAMA
        ]);
    }


}

