<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeadSource;

class LeadSourceController extends Controller
{
    /* ================================
     | CREATE FORM
     ================================= */
    public function create()
    {
        return view('crm.lead_sources.create');
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'nama_sumber' => 'required|string|max:255',
            'tipe'        => 'required|in:online,offline',
            'platform'    => 'nullable|string|max:50',
            'lokasi'      => 'nullable|string|max:255',
            'keterangan'  => 'nullable|string',
        ]);

        LeadSource::create($data);

        return redirect()
            ->route('crm.leads.create')
            ->with('success', 'Sumber lead berhasil ditambahkan.');
    }
}
