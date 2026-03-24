<?php

namespace App\Services\Agents;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AgentService
{
    /*
    |--------------------------------------------------------------------------
    | CREATE AGENT
    |--------------------------------------------------------------------------
    */

    public function create(array $data): Agent
    {
        $this->authorizeAgentCreation($data['branch_id']);

        return DB::transaction(function () use ($data) {

            // Prevent duplicate email
            if (User::where('email', $data['email'])->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'Email sudah digunakan.',
                ]);
            }

            // 1️⃣ Create User
            $user = User::create([
                'name'      => $data['nama'],
                'email'     => $data['email'],
                'password'  => Hash::make($data['password'] ?? Str::random(8)),
                'branch_id' => $data['branch_id'],
            ]);

            $user->assignRole('AGENT');

            // 2️⃣ Create Agent
            $agent = Agent::create([
                'user_id'    => $user->id,
                'branch_id'  => $data['branch_id'],
                'nama'       => $data['nama'],
                'kode_agent' => $this->generateUniqueAgentCode(),
                'slug'       => Str::slug($data['nama'] . '-' . uniqid()),
                'phone'      => $data['phone'] ?? null,
            ]);

            return $agent;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE AGENT
    |--------------------------------------------------------------------------
    */

    public function update(Agent $agent, array $data): Agent
    {
        $this->authorizeAgentCreation($data['branch_id']);

        return DB::transaction(function () use ($agent, $data) {

            $agent->update([
                'nama'      => $data['nama'],
                'phone'     => $data['phone'] ?? null,
                'branch_id' => $data['branch_id'],
            ]);

            $agent->user->update([
                'name'      => $data['nama'],
                'branch_id' => $data['branch_id'],
            ]);

            return $agent;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE AGENT
    |--------------------------------------------------------------------------
    */

    public function delete(Agent $agent): void
    {
        $this->authorizeAgentCreation($agent->branch_id);

        DB::transaction(function () use ($agent) {

            $user = $agent->user;

            $agent->delete();

            if ($user) {
                $user->delete(); // Explicit delete (lebih aman dari cascade)
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UNIQUE CODE GENERATOR
    |--------------------------------------------------------------------------
    */

    private function generateUniqueAgentCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (Agent::where('kode_agent', $code)->exists());

        return $code;
    }

    /*
    |--------------------------------------------------------------------------
    | AUTHORIZATION (Branch Isolation)
    |--------------------------------------------------------------------------
    */

    private function authorizeAgentCreation(int $branchId): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        // SUPERADMIN & ADMIN_PUSAT boleh semua
        if ($user->hasRole(['SUPERADMIN', 'ADMIN_PUSAT'])) {
            return;
        }

        // ADMIN_CABANG hanya boleh branch sendiri
        if ($user->hasRole('ADMIN_CABANG') && $user->branch_id === $branchId) {
            return;
        }

        abort(403);
    }
}