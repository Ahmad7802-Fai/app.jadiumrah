<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pipeline;
use App\Models\Lead;

class PipelineController extends Controller
{
    /** LIST PIPELINE */
    public function index()
    {
        $pipelines = Pipeline::orderBy('urutan')->get();
        return view('crm.pipeline.index', compact('pipelines'));
    }

    /** STORE */
    public function store(Request $req)
    {
        $req->validate([
            'tahap' => 'required|string|max:100',
            'urutan' => 'required|numeric',
            'aktif'  => 'nullable'
        ]);

        Pipeline::create([
            'tahap'  => strtolower(str_replace(' ', '_', $req->tahap)),
            'urutan' => $req->urutan,
            'aktif'  => $req->has('aktif') ? 1 : 0,
        ]);


        return back()->with('success', 'Pipeline stage ditambahkan.');
    }

    /** UPDATE */
    public function update(Request $req, $id)
    {
        $req->validate([
            'tahap' => 'required|string|max:100',
            'urutan' => 'required|numeric',
            'aktif'  => 'nullable'
        ]);

        Pipeline::findOrFail($id)->update([
            'tahap'  => strtolower(str_replace(' ', '_', $req->tahap)),
            'urutan' => $req->urutan,
            'aktif'  => $req->has('aktif') ? 1 : 0,
        ]);


        return back()->with('success', 'Pipeline stage diperbarui.');
    }

    /** DELETE */
    public function destroy($id)
    {
        Pipeline::findOrFail($id)->delete();

        return back()->with('success', 'Pipeline stage dihapus.');
    }

    /* =========================================
     | FORM GANTI PIPELINE (PER LEAD)
     ========================================= */
    public function change(Lead $lead)
    {
        $this->authorize('updatePipeline', $lead);

        $pipelines = Pipeline::where('aktif', 1)
            ->orderBy('urutan')
            ->get();

        return view('crm.pipeline.change', compact('lead', 'pipelines'));
    }

    /* =========================================
     | SIMPAN PIPELINE LEAD
     ========================================= */
    public function updateForLead(Request $request, Lead $lead)
    {
        $this->authorize('updatePipeline', $lead);

        $request->validate([
            'pipeline_id' => 'required|exists:pipelines,id',
        ]);

        $lead->update([
            'pipeline_id' => $request->pipeline_id,
        ]);

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('success', 'Pipeline berhasil diperbarui.');
    }

}
