<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use App\Models\Jamaah;
use App\Services\Jamaah\JamaahService;

class JamaahApprovalController extends Controller
{
    public function __construct(
        protected JamaahService $jamaahService
    ) {}

    public function index()
    {
        $jamaahs = Jamaah::where('approval_status', 'pending')
            ->latest()
            ->paginate(15);

        return view('jamaahs.approvals.index', compact('jamaahs'));
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    */
    public function approve(Jamaah $jamaah)
    {
        $this->jamaahService->approve($jamaah);

        return back()->with('success', 'Jamaah berhasil di-approve.');
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */
    public function reject(Jamaah $jamaah)
    {
        $this->jamaahService->reject($jamaah);

        return back()->with('success', 'Jamaah ditolak.');
    }
}