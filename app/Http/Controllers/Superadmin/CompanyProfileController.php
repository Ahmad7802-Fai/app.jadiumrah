<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Services\CompanyProfile\CompanyProfileService;
use Illuminate\Http\Request;

class CompanyProfileController extends Controller
{
    public function __construct(
        protected CompanyProfileService $service
    ) {}

    /* ===============================
     | INDEX (FORM)
     =============================== */
    public function index()
    {
        return view('superadmin.company-profile.index', [
            'company' => $this->service->getActive(),
        ]);
    }

    /* ===============================
     | STORE / UPDATE
     =============================== */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'brand_name'         => 'nullable|string|max:255',

            'email'              => 'nullable|email',
            'phone'              => 'nullable|string|max:50',
            'website'            => 'nullable|string|max:255',

            'address'            => 'nullable|string',
            'city'               => 'nullable|string|max:100',
            'province'           => 'nullable|string|max:100',
            'postal_code'        => 'nullable|string|max:20',

            'npwp'               => 'nullable|string|max:50',
            'npwp_name'          => 'nullable|string|max:255',
            'npwp_address'       => 'nullable|string',

            'bank_name'          => 'nullable|string|max:100',
            'bank_account_name'  => 'nullable|string|max:255',
            'bank_account_number'=> 'nullable|string|max:50',

            'invoice_footer'     => 'nullable|string',
            'letter_footer'      => 'nullable|string',

            'signature_name'     => 'nullable|string|max:255',
            'signature_position' => 'nullable|string|max:255',
        ]);

        $this->service->save($data);

        return back()->with('success', 'Company profile berhasil disimpan');
    }

    /* ===============================
     | UPLOAD LOGO
     =============================== */
    public function uploadLogo(Request $request, string $type)
    {
        $request->validate([
            'logo' => 'required|image|max:2048',
        ]);

        $company = CompanyProfile::active()->firstOrFail();

        $this->service->uploadLogo(
            $company,
            $request->file('logo'),
            $type
        );

        return back()->with('success', 'Logo berhasil diupload');
    }
}
