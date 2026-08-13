<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\CompanyBankAccount;
use App\Services\CompanyProfile\CompanyBankAccountService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CompanyBankAccountController extends Controller
{
    public function __construct(
        protected CompanyBankAccountService $service
    ) {}

    /* =====================================================
     * INDEX
     * ===================================================== */
public function index(Request $request)
{
    $purpose = $request->get('purpose', 'invoice');

    try {
        $banks = $this->service->list($purpose);
    } catch (ModelNotFoundException $e) {
        return redirect()
            ->route('superadmin.company-profile.create')
            ->with('warning', $e->getMessage());
    }

    return view('superadmin.company-bank.index', compact(
        'banks',
        'purpose'
    ));
}


    /* =====================================================
     * STORE
     * ===================================================== */
public function store(Request $request)
{
    $data = $request->validate([
        'bank_name'      => 'required|string|max:100',
        'account_number' => 'required|string|max:50',
        'account_name'   => 'required|string|max:100',
        'purpose'        => 'required|in:invoice,tabungan,refund,operational',
        'is_default'     => 'nullable|boolean',
    ]);

    if (!company()) {
        return redirect()
            ->route('superadmin.company-profile.create')
            ->with('warning', 'Company Profile belum tersedia. Silakan buat terlebih dahulu.');
    }

    $this->service->create([
        ...$data,
        'company_profile_id' => company()->id,
    ]);

    return back()->with('success', 'Rekening bank berhasil ditambahkan');
}

    /* =====================================================
     * SET DEFAULT
     * ===================================================== */
    public function setDefault(CompanyBankAccount $bank)
    {
        $this->service->setDefault($bank);

        return back()->with('success', 'Rekening default diperbarui');
    }

    /* =====================================================
     * DEACTIVATE
     * ===================================================== */
    public function deactivate(CompanyBankAccount $bank)
    {
        $this->service->deactivate($bank);

        return back()->with('success', 'Rekening berhasil dinonaktifkan');
    }

    public function activate(CompanyBankAccount $bank)
    {
        $bank->update([
            'is_active' => true,
        ]);

        return back()->with('success', 'Rekening bank berhasil diaktifkan kembali.');
    }

}
