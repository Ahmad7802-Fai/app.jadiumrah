<?php

namespace App\Http\Controllers\Jamaah;

use App\Http\Controllers\Controller;
use App\Models\Jamaah;
use App\Models\JamaahDocument;
use App\Models\Branch;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JamaahDocumentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX – MASTER DOCUMENT LIST (SECURE VERSION)
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $this->authorize('viewAny', JamaahDocument::class);

        $user = auth()->user();

        $query = JamaahDocument::with([
            'jamaah.branch',
            'jamaah.agent'
        ]);

        /*
        |--------------------------------------------------------------------------
        | 🔐 ROLE ISOLATION (WAJIB)
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('AGENT')) {

            $query->whereHas('jamaah', function ($q) use ($user) {
                $q->where('agent_id', $user->agent?->id);
            });

        } elseif ($user->hasRole('ADMIN_CABANG')) {

            $query->whereHas('jamaah', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });

        }
        // SUPERADMIN → no restriction


        /*
        |--------------------------------------------------------------------------
        | 🔎 FILTERS
        |--------------------------------------------------------------------------
        */

        if ($request->document_type) {
            $query->where('document_type', $request->document_type);
        }

        if ($request->expired === 'yes') {
            $query->whereDate('expired_at', '<', now());
        }

        // Filter branch (hanya berlaku untuk superadmin)
        if ($request->branch_id && $user->hasRole('SUPERADMIN')) {
            $query->whereHas('jamaah', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        // Filter agent (hanya berlaku untuk superadmin & admin cabang)
        if ($request->agent_id && !$user->hasRole('AGENT')) {
            $query->whereHas('jamaah', function ($q) use ($request) {
                $q->where('agent_id', $request->agent_id);
            });
        }

        $documents = $query->latest()->paginate(15);


        /*
        |--------------------------------------------------------------------------
        | 🔐 DROPDOWN DATA (SCOPE SAFE)
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('SUPERADMIN')) {

            $branches = Branch::active()->get();
            $agents   = Agent::active()->get();

        } elseif ($user->hasRole('ADMIN_CABANG')) {

            $branches = Branch::where('id', $user->branch_id)->get();
            $agents   = Agent::where('branch_id', $user->branch_id)
                        ->active()->get();

        } else { // AGENT

            $branches = collect();
            $agents   = Agent::where('id', $user->agent?->id)->get();
        }

        return view('jamaahs.documents.index', compact(
            'documents',
            'branches',
            'agents'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request, Jamaah $jamaah)
    {
        $this->authorize('update', $jamaah);

        $validated = $request->validate([
            'document_type' => 'required|string',
            'file'          => 'required|file|max:5120',
            'expired_at'    => 'nullable|date',
            'note'          => 'nullable|string|max:255',
        ]);

        $path = $request->file('file')
            ->store('jamaah-documents', 'public');

        JamaahDocument::create([
            'jamaah_id'    => $jamaah->id,
            'document_type'=> $validated['document_type'],
            'file_path'    => $path,
            'expired_at'   => $validated['expired_at'] ?? null,
            'note'         => $validated['note'] ?? null,
        ]);

        return back()->with('success', 'Dokumen berhasil diupload.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(JamaahDocument $document)
    {
        $this->authorize('delete', $document->jamaah);

        if ($document->file_path &&
            Storage::disk('public')->exists($document->file_path)) {

            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('success', 'Dokumen dihapus.');
    }
}