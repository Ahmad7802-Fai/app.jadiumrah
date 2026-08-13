<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Services\Lead\LeadPipelineService;
use Illuminate\Validation\ValidationException;

class LeadPipelineController extends Controller
{
    public function __construct(
        protected LeadPipelineService $service
    ) {}

    public function move(Lead $lead, Pipeline $pipeline)
    {
        abort_unless(auth()->user()->isAgent(), 403);

        // 🔒 ownership lead
        abort_unless(
            $lead->agent_id === auth()->user()->agent->id,
            403
        );

        try {
            $this->service->move(
                $lead,
                $pipeline,
                'MANUAL_AGENT',
                auth()->id()
            );

            return back()->with(
                'success',
                'Pipeline berhasil diperbarui.'
            );

        } catch (ValidationException $e) {

            return back()
                ->withErrors($e->errors())
                ->with(
                    'warning',
                    collect($e->errors())->flatten()->first()
                );
        }
    }
}
