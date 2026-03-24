<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\MarketingCampaign;
use App\Models\Paket;
use App\Services\Marketing\CampaignService;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function __construct(
        protected CampaignService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $this->authorize('viewAny', MarketingCampaign::class);

        $query = MarketingCampaign::withCount('bookings')
            ->with('pakets')
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $campaigns = $query->paginate(15);

        return view('marketing.campaigns.index', compact('campaigns'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $this->authorize('create', MarketingCampaign::class);

        $pakets = Paket::where('is_active', true)->get();

        return view('marketing.campaigns.create', compact('pakets'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $this->authorize('create', MarketingCampaign::class);

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'target_revenue'   => 'nullable|numeric|min:0',
            'budget_marketing' => 'nullable|numeric|min:0',
            'paket_ids'        => 'nullable|array',
            'paket_ids.*'      => 'exists:pakets,id',
        ]);

        $this->service->create($validated);

        return redirect()
            ->route('marketing.campaigns.index')
            ->with('success', 'Campaign berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(MarketingCampaign $campaign)
    {
        $this->authorize('update', $campaign);

        $pakets = Paket::where('is_active', true)->get();

        return view('marketing.campaigns.edit', compact(
            'campaign',
            'pakets'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, MarketingCampaign $campaign)
    {
        $this->authorize('update', $campaign);

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'target_revenue'   => 'nullable|numeric|min:0',
            'budget_marketing' => 'nullable|numeric|min:0',
            'paket_ids'        => 'nullable|array',
            'paket_ids.*'      => 'exists:pakets,id',
        ]);

        $this->service->update($campaign, $validated);

        return redirect()
            ->route('marketing.campaigns.index')
            ->with('success', 'Campaign berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIVATE
    |--------------------------------------------------------------------------
    */
    public function activate(MarketingCampaign $campaign)
    {
        $this->authorize('activate', $campaign);

        $this->service->activate($campaign);

        return back()->with('success', 'Campaign berhasil diaktifkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | FINISH
    |--------------------------------------------------------------------------
    */
    public function finish(MarketingCampaign $campaign)
    {
        $this->authorize('finish', $campaign);

        $this->service->finish($campaign);

        return back()->with('success', 'Campaign selesai.');
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL
    |--------------------------------------------------------------------------
    */
    public function cancel(MarketingCampaign $campaign)
    {
        $this->authorize('cancel', $campaign);

        $this->service->cancel($campaign);

        return back()->with('success', 'Campaign dibatalkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(MarketingCampaign $campaign)
    {
        $this->authorize('delete', $campaign);

        $campaign->delete();

        return back()->with('success', 'Campaign dihapus.');
    }
}