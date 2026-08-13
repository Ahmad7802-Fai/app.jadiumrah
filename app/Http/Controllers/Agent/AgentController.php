<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Branch;
use App\Services\Agents\AgentService;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function __construct(
        protected AgentService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = auth()->user();

        $query = Agent::with('branch','user');

        // 🔐 ROLE ISOLATION
        if ($user->hasRole('ADMIN_CABANG')) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($user->hasRole('AGENT')) {
            abort(403);
        }

        $agents = $query->latest()->get();

        return view('agents.index', compact('agents'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $user = auth()->user();

        if ($user->hasRole('AGENT')) {
            abort(403);
        }

        if ($user->hasRole('ADMIN_CABANG')) {
            $branches = Branch::where('id', $user->branch_id)->get();
        } else {
            $branches = Branch::all();
        }

        return view('agents.create', compact('branches'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('AGENT')) {
            abort(403);
        }

        $data = $request->validate([
            'nama'      => 'required|string',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'nullable|string|min:6',
            'branch_id' => 'required|exists:branches,id',
            'phone'     => 'nullable|string'
        ]);

        // 🔐 ADMIN CABANG tidak boleh buat agent di cabang lain
        if ($user->hasRole('ADMIN_CABANG')) {
            $data['branch_id'] = $user->branch_id;
        }

        $this->service->create($data);

        return redirect()->route('agents.index')
            ->with('success','Agent created.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Agent $agent)
    {
        $this->checkAccess($agent);

        $user = auth()->user();

        if ($user->hasRole('ADMIN_CABANG')) {
            $branches = Branch::where('id', $user->branch_id)->get();
        } else {
            $branches = Branch::all();
        }

        return view('agents.edit', compact('agent','branches'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Agent $agent)
    {
        $this->checkAccess($agent);

        $data = $request->validate([
            'nama'      => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'phone'     => 'nullable|string'
        ]);

        $user = auth()->user();

        // 🔐 ADMIN CABANG tidak boleh pindahkan cabang
        if ($user->hasRole('ADMIN_CABANG')) {
            $data['branch_id'] = $user->branch_id;
        }

        $this->service->update($agent, $data);

        return redirect()->route('agents.index')
            ->with('success','Agent updated.');
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(Agent $agent)
    {
        $this->checkAccess($agent);

        $this->service->delete($agent);

        return redirect()->route('agents.index')
            ->with('success','Agent deleted.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(Agent $agent)
    {
        $this->checkAccess($agent);

        return view('agents.show', compact('agent'));
    }

    /*
    |--------------------------------------------------------------------------
    | 🔐 CENTRAL ACCESS CHECK
    |--------------------------------------------------------------------------
    */

    protected function checkAccess(Agent $agent): void
    {
        $user = auth()->user();

        if ($user->hasRole('AGENT')) {
            abort(403);
        }

        if ($user->hasRole('ADMIN_CABANG') &&
            $agent->branch_id !== $user->branch_id) {

            abort(403);
        }
    }
}