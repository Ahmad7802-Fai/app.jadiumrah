<?php

namespace App\Services\CompanyProfile;

use App\Models\CompanyProfile;
use App\Models\CompanyBankAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use RuntimeException;

class CompanyBankAccountService
{
    /* =====================================================
     * RESOLVE ACTIVE COMPANY ID (SINGLE SOURCE OF TRUTH)
     * ===================================================== */
    protected function companyId(?int $companyProfileId = null): int
    {
        // Jika dikirim eksplisit (misal dari superadmin)
        if ($companyProfileId) {
            return $companyProfileId;
        }

        $company = CompanyProfile::where('is_active', 1)->first();

        if (!$company) {
            throw new ModelNotFoundException(
                'Company Profile belum tersedia. 
                Silakan buat Company Profile terlebih dahulu dan set sebagai aktif.'
            );
        }

        return $company->id;
    }

    /* =====================================================
     * GET DEFAULT BANK BY PURPOSE
     * ===================================================== */
    public function getDefault(
        string $purpose = 'invoice',
        ?int $companyProfileId = null
    ): ?CompanyBankAccount {

        return CompanyBankAccount::where(
                'company_profile_id',
                $this->companyId($companyProfileId)
            )
            ->where('purpose', $purpose)
            ->where('is_active', 1)
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->first();
    }

    /* =====================================================
     * LIST BANK ACCOUNTS
     * ===================================================== */
    public function list(
        string $purpose = 'invoice',
        ?int $companyProfileId = null
    ) {
        return CompanyBankAccount::where(
                'company_profile_id',
                $this->companyId($companyProfileId)
            )
            ->where('purpose', $purpose)
            ->orderByDesc('is_default')
            ->orderBy('bank_name')
            ->get();
    }

    /* =====================================================
     * CREATE BANK ACCOUNT
     * ===================================================== */
    public function create(array $data): CompanyBankAccount
    {
        return DB::transaction(function () use ($data) {

            if (!empty($data['is_default'])) {
                $this->unsetDefault(
                    $data['company_profile_id'],
                    $data['purpose']
                );
            }

            $bank = CompanyBankAccount::create([
                'company_profile_id' => $data['company_profile_id'],
                'bank_name'          => $data['bank_name'],
                'account_number'     => $data['account_number'],
                'account_name'       => $data['account_name'],
                'purpose'            => $data['purpose'],
                'is_default'         => $data['is_default'] ?? false,
                'is_active'          => $data['is_active'] ?? true,
            ]);

            $this->clearCache($bank->purpose);

            return $bank;
        });
    }

    /* =====================================================
     * UPDATE BANK ACCOUNT
     * ===================================================== */
    public function update(
        CompanyBankAccount $bank,
        array $data
    ): CompanyBankAccount {

        return DB::transaction(function () use ($bank, $data) {

            if (!empty($data['is_default'])) {
                $this->unsetDefault(
                    $bank->company_profile_id,
                    $bank->purpose,
                    $bank->id
                );
            }

            $bank->update([
                'bank_name'      => $data['bank_name'],
                'account_number' => $data['account_number'],
                'account_name'   => $data['account_name'],
                'is_default'     => $data['is_default'] ?? false,
                'is_active'      => $data['is_active'] ?? true,
            ]);

            $this->clearCache($bank->purpose);

            return $bank;
        });
    }

    /* =====================================================
     * SET DEFAULT BANK
     * ===================================================== */
    public function setDefault(CompanyBankAccount $bank): void
    {
        DB::transaction(function () use ($bank) {

            $this->unsetDefault(
                $bank->company_profile_id,
                $bank->purpose,
                $bank->id
            );

            $bank->update(['is_default' => true]);

            $this->clearCache($bank->purpose);
        });
    }

    /* =====================================================
     * ACTIVATE / DEACTIVATE
     * ===================================================== */
    public function activate(CompanyBankAccount $bank): void
    {
        $bank->update(['is_active' => true]);
    }

    public function deactivate(CompanyBankAccount $bank): void
    {
        $bank->update([
            'is_active'  => false,
            'is_default' => false,
        ]);

        $this->clearCache($bank->purpose);
    }

    /* =====================================================
     * INTERNAL: UNSET DEFAULT
     * ===================================================== */
    protected function unsetDefault(
        int $companyProfileId,
        string $purpose,
        ?int $exceptId = null
    ): void {

        CompanyBankAccount::where('company_profile_id', $companyProfileId)
            ->where('purpose', $purpose)
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->update(['is_default' => false]);
    }

    /* =====================================================
     * CACHE HANDLER
     * ===================================================== */
    protected function clearCache(string $purpose): void
    {
        Cache::forget("company_bank_default_{$purpose}");
    }
}
