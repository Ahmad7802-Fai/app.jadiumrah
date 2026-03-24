<?php

namespace App\Services\Company;

use App\Models\CompanyBankAccount;
use Illuminate\Support\Facades\DB;

class CompanyBankService
{
    public function create(array $data): CompanyBankAccount
    {
        return CompanyBankAccount::create($data);
    }

    public function update(CompanyBankAccount $bank, array $data): CompanyBankAccount
    {
        $bank->update($data);
        return $bank;
    }

    public function delete(CompanyBankAccount $bank): void
    {
        $bank->delete();
    }

    public function setDefault(int $companyId, int $bankId): void
    {
        DB::transaction(function () use ($companyId, $bankId) {

            CompanyBankAccount::where('company_profile_id', $companyId)
                ->update(['is_default' => false]);

            CompanyBankAccount::where('id', $bankId)
                ->update(['is_default' => true]);
        });
    }
}