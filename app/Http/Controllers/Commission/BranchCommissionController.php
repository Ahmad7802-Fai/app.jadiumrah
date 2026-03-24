<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Services\Commission\BranchCommissionService;
use Illuminate\Http\Request;

class BranchCommissionController extends Controller
{
    protected BranchCommissionService $service;

    public function __construct(BranchCommissionService $service)
    {
        $this->service = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX PAGE
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $data = $this->service->getAllBranchConfigs();

        return view('commission.config', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE COMPANY
    |--------------------------------------------------------------------------
    */

    public function updateCompany(Request $request, $branchId)
    {
        $validated = $request->validate([
            'amount_per_closing' => 'required|numeric|min:0',
        ]);

        $this->service->updateCompany(
            $branchId,
            $validated['amount_per_closing']
        );

        return back()->with('success', 'Company rule updated.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE AGENT
    |--------------------------------------------------------------------------
    */

    public function updateAgent(Request $request, $branchId)
    {
        $validated = $request->validate([
            'agent_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $this->service->updateAgent(
            $branchId,
            $validated['agent_percentage']
        );

        return back()->with('success', 'Agent rule updated.');
    }
}