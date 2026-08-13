<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\MarketingExpenses;
use App\Services\Marketing\MarketingExpenseService;
use Illuminate\Http\Request;

class MarketingExpenseController extends Controller
{
    public function __construct(
        protected MarketingExpenseService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
public function index(Request $request)
{
    $filters = $request->only(['bulan', 'platform']);

    $expenses = $this->service->list($filters);
    $summary  = $this->service->summary($filters);
    $cplData  = $this->service->costPerLead($filters);
$roiData = $this->service->roiMarketing($filters);

return view('marketing.expenses.index', compact(
    'expenses',
    'summary',
    'cplData',
    'roiData'
));
}

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('marketing.expenses.create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $data = $request->validate([
            'sumber_id'     => 'required|integer',
            'nama_campaign' => 'nullable|string|max:255',
            'platform'      => 'nullable|string',
            'biaya'         => 'required|numeric|min:0',
            'tanggal'       => 'required|date',
            'catatan'       => 'nullable|string',
        ]);

        $this->service->create($data);

        return redirect()
            ->route('marketing.expenses.index')
            ->with('success', 'Biaya marketing berhasil ditambahkan');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(MarketingExpenses $expense)
    {
        return view('marketing.expenses.edit', compact('expense'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, MarketingExpenses $expense)
    {
        $data = $request->validate([
            'sumber_id'     => 'required|integer',
            'nama_campaign' => 'nullable|string|max:255',
            'platform'      => 'nullable|string',
            'biaya'         => 'required|numeric|min:0',
            'tanggal'       => 'required|date',
            'catatan'       => 'nullable|string',
        ]);

        $this->service->update($expense, $data);

        return redirect()
            ->route('marketing.expenses.index')
            ->with('success', 'Biaya marketing berhasil diperbarui');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(MarketingExpenses $expense)
    {
        $this->service->delete($expense);

        return back()
            ->with('success', 'Biaya marketing berhasil dihapus');
    }
}
