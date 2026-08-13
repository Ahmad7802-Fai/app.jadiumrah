<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\PipelineLog;

class PipelineLogController extends Controller
{
    /** LIST PIPELINE LOGS */
    public function index()
    {
        $logs = PipelineLog::with(['lead', 'user'])
            ->orderBy('changed_at', 'desc')
            ->get();

        return view('crm.pipeline.logs', compact('logs'));
    }
}
