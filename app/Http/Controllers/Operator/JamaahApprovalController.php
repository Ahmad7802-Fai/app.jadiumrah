<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Jamaah;
use App\Services\Jamaah\JamaahApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Exception;

class JamaahApprovalController extends Controller
{
    public function __construct(
        protected JamaahApprovalService $service
    ) {}
    // =====================================================
    // LIST JAMAAH PENDING APPROVAL
    // =====================================================
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $search = $request->get('q');

        $jamaahs = Jamaah::with('branch')
            ->where('status', $status)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('no_id', 'like', "%{$search}%")
                    ->orWhereHas('branch', function ($b) use ($search) {
                            $b->where('nama_cabang', 'like', "%{$search}%");
                    });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString(); // 🔥 PENTING: jaga query saat pagination

        return view(
            'operator.jamaah-approval.index',
            compact('jamaahs', 'status', 'search')
        );
    }

    // =====================================================
    // SHOW JAMAAH DETAILS
    // ===================================================== 
    public function show(int $id)
    {
        $jamaah = Jamaah::findOrFail($id);
        $this->authorize('approve', $jamaah);

        return view('operator.jamaah-approval.show', compact('jamaah'));
    }

    /* =====================================================
     | APPROVE JAMAAH
     ===================================================== */
    public function approve(int $id)
    {
        $jamaah = Jamaah::findOrFail($id);

        // Authorization (Policy)
        $this->authorize('approve', $jamaah);

        try {
            $this->service->approve($jamaah);

            return redirect()
                ->back()
                ->with('success', 'Jamaah berhasil disetujui.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /* =====================================================
     | REJECT JAMAAH
     ===================================================== */
    public function reject(Request $request, int $id)
    {
        $jamaah = Jamaah::findOrFail($id);

        // Authorization (Policy)
        $this->authorize('approve', $jamaah);

        // Alasan wajib
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        try {
            $this->service->reject($jamaah, $request->reason);

            return redirect()
                ->back()
                ->with('success', 'Jamaah berhasil ditolak.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
    /* =====================================================
     | BULK APPROVE JAMAAH
     ===================================================== */   
    public function bulkApprove(Request $request)
    {
        $ids = $request->input('jamaah_ids', []);

        if (count($ids) === 0) {
            return back()->with('error', 'Tidak ada jamaah yang dipilih.');
        }

        DB::transaction(function () use ($ids) {

            $jamaahs = Jamaah::whereIn('id', $ids)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->get();

            foreach ($jamaahs as $jamaah) {

                // 🔐 POLICY CHECK PER ITEM
                $this->authorize('approve', $jamaah);

                $jamaah->update([
                    'status'      => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);

                // 🧾 AUDIT
                \App\Services\Jamaah\JamaahAuditService::log(
                    $jamaah,
                    'APPROVED',
                    ['status' => 'pending'],
                    ['status' => 'approved']
                );
            }
        });

        return back()->with('success', 'Jamaah terpilih berhasil di-approve.');
    }

}
