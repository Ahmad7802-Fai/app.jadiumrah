<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\MarketingBanner;
use App\Models\MarketingCampaign;
use App\Models\Branch;
use App\Services\Marketing\BannerService;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function __construct(
        protected BannerService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $this->authorize('viewAny', MarketingBanner::class);

        $query = MarketingBanner::with(['campaign','branch'])
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->page) {
            $query->where('page', $request->page);
        }

        $banners = $query->paginate(15);

        return view('marketing.banners.index', compact('banners'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $this->authorize('create', MarketingBanner::class);

        $campaigns = MarketingCampaign::where('status','active')->get();
        $branches  = Branch::all();

        return view('marketing.banners.create', compact(
            'campaigns',
            'branches'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $this->authorize('create', MarketingBanner::class);

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'subtitle'      => 'nullable|string|max:255',
            'image'         => 'required|image|max:2048',
            'mobile_image'  => 'nullable|image|max:2048',
            'link'          => 'nullable|string',
            'link_type'     => 'required|in:internal,external',
            'page'          => 'required|string',
            'position'      => 'required|string',
            'sort_order'    => 'nullable|integer',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'campaign_id'   => 'nullable|exists:marketing_campaigns,id',
            'target_role'   => 'nullable|string',
            'target_branch_id' => 'nullable|exists:branches,id',
        ]);

        $this->service->create($validated);

        return redirect()
            ->route('marketing.banners.index')
            ->with('success','Banner berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(MarketingBanner $banner)
    {
        $this->authorize('update', $banner);

        $campaigns = MarketingCampaign::all();
        $branches  = Branch::all();

        return view('marketing.banners.edit', compact(
            'banner',
            'campaigns',
            'branches'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, MarketingBanner $banner)
    {
        $this->authorize('update', $banner);

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'subtitle'      => 'nullable|string|max:255',
            'image'         => 'nullable|image|max:2048',
            'mobile_image'  => 'nullable|image|max:2048',
            'link'          => 'nullable|string',
            'link_type'     => 'required|in:internal,external',
            'page'          => 'required|string',
            'position'      => 'required|string',
            'sort_order'    => 'nullable|integer',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
            'campaign_id'   => 'nullable|exists:marketing_campaigns,id',
            'target_role'   => 'nullable|string',
            'target_branch_id' => 'nullable|exists:branches,id',
        ]);

        $this->service->update($banner, $validated);

        return redirect()
            ->route('marketing.banners.index')
            ->with('success','Banner berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLISH
    |--------------------------------------------------------------------------
    */
    public function publish(MarketingBanner $banner)
    {
        $this->authorize('update', $banner);

        $this->service->publish($banner);

        return back()->with('success','Banner dipublish.');
    }

    /*
    |--------------------------------------------------------------------------
    | ARCHIVE
    |--------------------------------------------------------------------------
    */
    public function archive(MarketingBanner $banner)
    {
        $this->authorize('update', $banner);

        $this->service->archive($banner);

        return back()->with('success','Banner diarsipkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(MarketingBanner $banner)
    {
        $this->authorize('delete', $banner);

        $this->service->delete($banner);

        return back()->with('success','Banner dihapus.');
    }
}