<?php

namespace App\Http\Controllers\Cabang;

use App\Http\Controllers\Controller;
use App\Models\Jamaah;
use App\Models\Agent;
use App\Models\Keberangkatan;
use App\Services\JamaahService;
use Illuminate\Http\Request;
use App\Support\PrintContext;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Invoices;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\Invoice\InvoiceViewBuilder;
use App\Support\CompanyProfile;

use Exception;

class JamaahCabangController extends Controller
{
    use CompanyProfile;
    public function __construct(
        protected JamaahService $jamaahService
    ) {}

    /* =====================================================
     | INDEX
     ===================================================== */
    public function index(Request $request)
    {
        $query = Jamaah::with(['agent.user'])
            ->where('branch_id', auth()->user()->branch_id);

        // 🔍 Search
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($s) use ($q) {
                $s->where('nama_lengkap', 'like', "%{$q}%")
                  ->orWhere('nik', 'like', "%{$q}%")
                  ->orWhere('no_hp', 'like', "%{$q}%")
                  ->orWhere('no_id', 'like', "%{$q}%");
            });
        }

        // 🎯 Filter agent
        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        $jamaah = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $agents = Agent::with('user')
            ->where('branch_id', auth()->user()->branch_id)
            ->where('is_active', 1)
            ->get();

        return view('cabang.jamaah.index', compact('jamaah','agents'));
    }

    /* =====================================================
     | CREATE
     ===================================================== */
    public function create()
    {
        $agents = Agent::where('branch_id', auth()->user()->branch_id)
            ->where('is_active',1)
            ->get();

        $keberangkatan = Keberangkatan::where('status','Aktif')
            ->orderBy('tanggal_berangkat')
            ->get();

        $autoNoID = $this->jamaahService->generateNoId();

        return view('cabang.jamaah.create', compact(
            'agents',
            'keberangkatan',
            'autoNoID'
        ));
    }

    /* =====================================================
     | STORE
     ===================================================== */
    public function store(Request $request)
    {
        try {
            $data = $request->except(['_token','_method']);

            $this->jamaahService->create($data);

            return redirect()
                ->route('cabang.jamaah.index')
                ->with('success', 'Jamaah berhasil ditambahkan (menunggu approval pusat).');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
    /* =====================================================
     | EDIT
     ===================================================== */

    public function edit($id)
    {
        $jamaah = Jamaah::withoutGlobalScopes()
            ->with(['agent.user', 'keberangkatan.paketMaster'])
            ->findOrFail($id);

        // 🔒 HARD AUTHORIZATION
        abort_unless(
            $jamaah->branch_id === auth()->user()->branch_id,
            403,
            'Jamaah bukan milik cabang Anda'
        );

        $agents = Agent::with('user')
            ->where('branch_id', auth()->user()->branch_id)
            ->where('is_active', 1)
            ->get();

        $keberangkatan = Keberangkatan::with('paketMaster')
            ->orderBy('tanggal_berangkat')
            ->get();

        return view('cabang.jamaah.edit', compact(
            'jamaah',
            'agents',
            'keberangkatan'
        ));
    }
    /* =====================================================
     | UPDATE
     ===================================================== */
    public function update(Request $request, $id)
    {
        $jamaah = Jamaah::withoutGlobalScopes()
            ->findOrFail($id);

        abort_unless(
            $jamaah->branch_id === auth()->user()->branch_id,
            403,
            'Jamaah bukan milik cabang Anda'
        );

        try {
            $data = $request->except(['_token', '_method']);

            $this->jamaahService->update($jamaah->id, $data);

            return redirect()
                ->route('cabang.jamaah.index')
                ->with('success', 'Data jamaah berhasil diperbarui.');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
    /* =====================================================
     | SHOW
     ===================================================== */
    public function show(Jamaah $jamaah)
    {
        // 🔐 Authorization (Policy)
        $this->authorize('view', $jamaah);

        // 🔒 Guard tambahan: cabang hanya akses data sendiri
        abort_if(
            auth()->user()->branch_id !== $jamaah->branch_id,
            403,
            'Akses ditolak'
        );

        // 🔒 CEK PEMBAYARAN PENDING (MENUNGGU APPROVAL PUSAT)
        $hasPendingPayment = $jamaah->payments()
            ->where('status', 'pending')
            ->where('is_deleted', 0)
            ->exists();

        // 📦 Eager load relasi yang dibutuhkan
        $jamaah->load([
            'branch',
            'agent.user',
            'paketMaster',
            'tabungan',
            'payments' => function ($q) {
                $q->where('status', 'valid')
                ->where('is_deleted', 0)
                ->orderBy('tanggal_bayar', 'asc');
            },
        ]);

        // 🖥️ Render view
        return view(
            'cabang.jamaah.show',
            compact('jamaah', 'hasPendingPayment')
        );
    }

    /* =====================================================
     | PRINT (PDF)
     ===================================================== */
    public function printDetail(int $id)
    {
        $jamaah = Jamaah::withoutGlobalScope('access')
            ->with(['branch','agent.user','paketMaster','keberangkatan'])
            ->findOrFail($id);

        $this->authorize('view', $jamaah);

        abort_if(
            auth()->user()->branch_id !== $jamaah->branch_id,
            403
        );

        $ctx = PrintContext::fromRole(auth()->user()->role);

        // 🔐 HASH ANTI PALSU
        $hash = sha1(
            $jamaah->id .
            $jamaah->no_id .
            $jamaah->created_at
        );

        // ✅ QR API (PNG) → BASE64 (PDF SAFE)
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='
            . urlencode($hash);

        $qrCode = 'data:image/png;base64,' . base64_encode(
            file_get_contents($qrUrl)
        );

        return Pdf::loadView(
            'shared.jamaah.jamaah-detail',
            compact('jamaah', 'ctx', 'qrCode')
        )
        ->setPaper('A4', 'portrait')
        ->stream("jamaah-{$jamaah->no_id}.pdf");
    }


    // =====================================================
    // | PRINT INVOICE JAMAAH (PDF)
    // =====================================================
    public function printInvoice(
        int $jamaah,
        InvoiceViewBuilder $builder
    ) {
        /*
        |--------------------------------------------------------------------------
        | 1️⃣ AMBIL JAMAAH TANPA GLOBAL SCOPE
        |--------------------------------------------------------------------------
        */
        $jamaah = Jamaah::withoutGlobalScope('access')
            ->findOrFail($jamaah);

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ VALIDASI CABANG (SINGLE SOURCE OF TRUTH)
        |--------------------------------------------------------------------------
        */
        $ctx = app('access.context');

        if (
            empty($ctx['branch_id']) ||
            (int) $ctx['branch_id'] !== (int) $jamaah->branch_id
        ) {
            abort(403, 'ANDA TIDAK BERHAK MENCETAK INVOICE JAMAAH INI');
        }

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ INVOICE SAH (HASIL APPROVAL PUSAT)
        |--------------------------------------------------------------------------
        */
        $invoice = $jamaah->invoices()
            ->whereIn('status', ['lunas', 'cicilan', 'belum_lunas'])
            ->latest()
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ BUILD DATA
        |--------------------------------------------------------------------------
        */
        $data = $builder->build($invoice, $jamaah);

        /*
        |--------------------------------------------------------------------------
        | 5️⃣ RENDER PDF
        |--------------------------------------------------------------------------
        */
        return Pdf::loadView(
            'shared.invoice.print',
            $data
        )
        ->setPaper('A4')
        ->stream("invoice-{$invoice->nomor_invoice}.pdf");
    }


// public function printInvoice(int $jamaah_id)
// {
//     $ctx = app('access.context');

//     // 🔓 AMBIL JAMAAH TANPA GLOBAL SCOPE
//     $jamaah = Jamaah::withoutGlobalScope('access')
//         ->findOrFail($jamaah_id);

//     // 1️⃣ VALIDASI CABANG
//     if (
//         empty($ctx['branch_id']) ||
//         (int) $ctx['branch_id'] !== (int) $jamaah->branch_id
//     ) {
//         abort(403, 'ANDA TIDAK BERHAK MENCETAK INVOICE JAMAAH INI');
//     }

//     // 2️⃣ AMBIL INVOICE LANGSUNG (TANPA JAMAAH SCOPE)
//     $invoice = Invoices::where('jamaah_id', $jamaah->id)
//         ->whereIn('status', ['lunas', 'cicilan', 'belum_lunas'])
//         ->firstOrFail();

//     $history = $invoice->payments()
//         ->where('status', 'valid')
//         ->where('is_deleted', 0)
//         ->get();

//     return \Barryvdh\DomPDF\Facade\Pdf::loadView(
//         'shared.invoice.print',
//         [
//             'invoice' => $invoice,
//             'jamaah'  => $jamaah,
//             'history' => $history,
//             'company' => $this->companyProfile(),
//         ]
//     )
//     ->setPaper('A4', 'portrait')
//     ->stream("invoice-{$invoice->nomor_invoice}.pdf");
// }

}
