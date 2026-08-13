<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function index(Request $req)
    {
        $q = $req->q;

        $team = Team::when($q, fn($qr) => $qr->where('nama', 'like', "%$q%"))
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);

        return view('admin.team.index', compact('team'));
    }

    public function create()
    {
        return view('admin.team.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nama', 'jabatan']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('team', 'public');
        }

        Team::create($data);

        return redirect()->route('admin.team.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }



    public function edit(Team $team)
    {
        return view('admin.team.edit', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nama', 'jabatan']);

        if ($request->hasFile('photo')) {

            if ($team->photo && file_exists(storage_path('app/public/'.$team->photo))) {
                unlink(storage_path('app/public/'.$team->photo));
            }

            $data['photo'] = $request->file('photo')->store('team', 'public');
        }

        $team->update($data);

        return redirect()->route('admin.team.index')
            ->with('success', 'Data berhasil diupdate.');
    }


    public function destroy(Team $team)
    {
        if ($team->photo && Storage::disk('public')->exists($team->photo)) {
            Storage::disk('public')->delete($team->photo);
        }

        $team->delete();

        return back()->with('success', 'Team berhasil dihapus.');
    }
}
