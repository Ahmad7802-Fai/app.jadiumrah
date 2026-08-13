<?php

namespace App\Services;

use App\Models\Jamaah;
use App\Models\Keberangkatan;
use App\Models\Payments;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\Jamaah\JamaahAuditService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Exception;

class JamaahService
{
    /* ============================================================
     | VALIDASI FIELD WAJIB
     ============================================================ */
    private function guardRequired(array $data): void
    {
        $required = [
            'id_keberangkatan',
            'no_id',
            'nama_lengkap',
            'nama_ayah',
            'nik',
            'no_hp',
            'tempat_lahir',
            'tanggal_lahir',
            'status_pernikahan',
            'jenis_kelamin',
            'tipe_kamar',
        ];

        foreach ($required as $key) {
            if (!array_key_exists($key, $data)) {
                throw new Exception("Field '{$key}' tidak terkirim.");
            }
        }
    }

    private function guardCreate(array $data): void
    {
        $required = [
            'id_keberangkatan',
            'nama_lengkap',
            'nama_ayah',
            'nik',
            'no_hp',
            'tempat_lahir',
            'tanggal_lahir',
            'status_pernikahan',
            'jenis_kelamin',
            'tipe_kamar',
        ];

        foreach ($required as $key) {
            if (!array_key_exists($key, $data)) {
                throw new Exception("Field '{$key}' tidak terkirim.");
            }
        }
    }

    private function guardUpdate(array $data): void
    {
        $required = [
            'nama_lengkap',
            'nama_ayah',
            'nik',
            'no_hp',
            'tempat_lahir',
            'tanggal_lahir',
            'status_pernikahan',
            'jenis_kelamin',
        ];

        foreach ($required as $key) {
            if (!array_key_exists($key, $data)) {
                throw new Exception("Field '{$key}' tidak terkirim.");
            }
        }
    }


    /* ============================================================
     | CREATE JAMAAH
     ============================================================ */
    public function create(array $data): Jamaah
    {
        $this->guardCreate($data);

        return DB::transaction(function () use ($data) {

            /* =====================================================
             | ACCESS CONTEXT (SINGLE SOURCE OF TRUTH)
             ===================================================== */
            $ctx = app('access.context');

            $role     = strtoupper($ctx['role'] ?? '');
            $branchId = $ctx['branch_id'] ?? null;
            $agentId  = $ctx['agent_id'] ?? null;

            Log::info('🧠 CREATE JAMAAH CONTEXT', compact(
                'role', 'branchId', 'agentId'
            ));

            /* =====================================================
             | HARD GUARD ROLE
             ===================================================== */
            if ($role === 'ADMIN' && !$branchId) {
                throw new Exception('Admin wajib terikat ke cabang.');
            }

            if ($role === 'SALES' && (!$branchId || !$agentId)) {
                throw new Exception('Sales wajib terikat ke cabang & agent.');
            }

            /* =====================================================
             | RESOLVE CONTEXT KE DATA
             ===================================================== */
            $resolvedBranchId = in_array($role, ['ADMIN', 'SALES'])
                ? $branchId
                : null; // OPERATOR / SUPERADMIN pusat

            $resolvedAgentId = $role === 'SALES'
                ? $agentId
                : null;

            /* =====================================================
             | VALIDASI KEBERANGKATAN & PAKET
             ===================================================== */
            $keberangkatan = Keberangkatan::with('paketMaster')
                ->where('status', 'Aktif')
                ->findOrFail($data['id_keberangkatan']);

            $paket = $keberangkatan->paketMaster;
            if (!$paket) {
                throw new Exception('Paket belum diset.');
            }

            /* =====================================================
             | HITUNG USIA
             ===================================================== */
            $usia = Carbon::parse($data['tanggal_lahir'])->age;

            $kelompokUsia = match (true) {
                $usia <= 12 => 'Anak',
                $usia <= 50 => 'Dewasa',
                default     => 'Lansia',
            };

            /* =====================================================
             | HARGA
             ===================================================== */
            $hargaDefault = $this->resolveHarga(
                $paket,
                $data['tipe_kamar']
            );

            /* =====================================================
             | FOTO
             ===================================================== */
            $fotoPath = null;

            if (
                isset($data['foto']) &&
                $data['foto'] instanceof UploadedFile
            ) {
                $fotoPath = $data['foto']->store('jamaah', 'public');
            }
            /* =====================================================
            | AUTO NUMBER NO ID
            ===================================================== */
            $noId = $this->generateNoId();
            /* =====================================================
             | CREATE JAMAAH
             ===================================================== */
            return Jamaah::create([
                // CONTEXT
                'branch_id' => $resolvedBranchId,
                'agent_id'  => $resolvedAgentId,
                'status'    => 'pending',

                // RELASI
                'id_keberangkatan' => $keberangkatan->id,
                'id_paket_master'  => $paket->id,

                // PAKET
                'nama_paket' => $paket->nama_paket,
                'paket'      => $paket->nama_paket,
                'tipe_kamar' => $data['tipe_kamar'],
                'tipe_jamaah' => $data['tipe_jamaah'] ?? 'reguler', // ✅ FIX
                // HARGA
                'harga_default' => $hargaDefault,
                'diskon'        => $data['diskon'] ?? 0,

                // IDENTITAS
                'no_id' => $this->generateNoId(),
                'nama_lengkap' => $data['nama_lengkap'],
                'nama_passport'=> $data['nama_passport'] ?? null,
                'nama_ayah'    => $data['nama_ayah'],
                'nik'          => $data['nik'],
                'no_hp'        => $data['no_hp'],

                'tempat_lahir'  => $data['tempat_lahir'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'usia'          => $usia,
                'kelompok_usia' => $kelompokUsia,

                'status_pernikahan' => $data['status_pernikahan'],
                'jenis_kelamin'     => $data['jenis_kelamin'],

                // MAHRAM
                'nama_mahram'   => $data['nama_mahram'] ?? null,
                'status_mahram' => $data['status_mahram'] ?? null,

                // SCREENING (INI YANG HILANG TOTAL)
                'pernah_umroh'    => $data['pernah_umroh'] ?? 'Tidak',
                'pernah_haji'     => $data['pernah_haji'] ?? 'Tidak',
                'merokok'         => $data['merokok'] ?? 'Tidak',
                'penyakit_khusus' => $data['penyakit_khusus'] ?? 'Tidak',
                'kursi_roda'      => $data['kursi_roda'] ?? 'Tidak',
                'nama_penyakit'   => $data['nama_penyakit'] ?? null,

                // LAINNYA
                'foto'       => $fotoPath,
                'keterangan' => $data['keterangan'] ?? null,
            ]);
        });
    }

    /* ============================================================
     | UPDATE JAMAAH
     ============================================================ */
    public function update(int $jamaahId, array $data): Jamaah
    {
        $this->guardUpdate($data);

        return DB::transaction(function () use ($jamaahId, $data) {

            /* =====================================================
            | LOCK JAMAAH
            ===================================================== */
            $jamaah = Jamaah::lockForUpdate()->findOrFail($jamaahId);

            /* =====================================================
            | SNAPSHOT DATA LAMA (AUDIT)
            ===================================================== */
            $old = $jamaah->getOriginal();

            /* =====================================================
            | CEK PEMBAYARAN
            ===================================================== */
            $hasPayment = Payments::where('jamaah_id', $jamaah->id)
                ->where('status', 'valid')
                ->where('is_deleted', 0)
                ->exists();

            /* =====================================================
            | HITUNG USIA
            ===================================================== */
            $usia = Carbon::parse($data['tanggal_lahir'])->age;

            /* =====================================================
            | FOTO
            ===================================================== */
            $fotoPath = $jamaah->foto;
            if (!empty($data['foto'])) {
                if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                    Storage::disk('public')->delete($fotoPath);
                }
                $fotoPath = $data['foto']->store('jamaah', 'public');
            }
            /* =====================================================
            | PAYLOAD DASAR (SELALU ADA)
            ===================================================== */
            $payload = [
                'nama_lengkap'      => $data['nama_lengkap'],
                'nama_ayah'         => $data['nama_ayah'],
                'nik'               => $data['nik'],
                'no_hp'             => $data['no_hp'],
                'tempat_lahir'      => $data['tempat_lahir'],
                'tanggal_lahir'     => $data['tanggal_lahir'],
                'usia'              => $usia,
                'status_pernikahan' => $data['status_pernikahan'],
                'jenis_kelamin'     => $data['jenis_kelamin'],
                'foto'              => $fotoPath,
                'keterangan'        => $data['keterangan'] ?? null,
            ];

            // ✅ TIPE JAMAAH (HANYA JIKA BELUM ADA PAYMENT)
            if (!$hasPayment && isset($data['tipe_jamaah'])) {
                $payload['tipe_jamaah'] = $data['tipe_jamaah'];
            }

            /* =====================================================
            | SCREENING (UPDATE HANYA JIKA DIKIRIM)
            ===================================================== */
            foreach ([
                'pernah_umroh',
                'pernah_haji',
                'merokok',
                'penyakit_khusus',
                'kursi_roda',
            ] as $field) {
                if (array_key_exists($field, $data)) {
                    $payload[$field] = $data[$field];
                }
            }

            /* =====================================================
            | OPSIONAL BERTAHAP (JANGAN OVERWRITE NULL)
            ===================================================== */
            foreach ([
                'nama_passport',
                'nama_mahram',
                'status_mahram',
                'nama_penyakit',
            ] as $field) {
                if (
                    array_key_exists($field, $data) &&
                    $data[$field] !== null &&
                    $data[$field] !== ''
                ) {
                    $payload[$field] = $data[$field];
                }
            }

            /* =====================================================
            | PAKET & HARGA (JIKA BELUM ADA PEMBAYARAN)
            ===================================================== */
            if (
                !$hasPayment &&
                isset($data['id_keberangkatan'], $data['tipe_kamar'])
            ) {
                $keberangkatan = Keberangkatan::with('paketMaster')
                    ->findOrFail($data['id_keberangkatan']);

                $paket = $keberangkatan->paketMaster;
                if (!$paket) {
                    throw new Exception('Paket belum diset.');
                }

                $payload += [
                    'id_keberangkatan' => $keberangkatan->id,
                    'id_paket_master'  => $paket->id,
                    'nama_paket'       => $paket->nama_paket,
                    'paket'            => $paket->nama_paket,
                    'tipe_kamar'       => $data['tipe_kamar'],
                    'harga_default'    => $this->resolveHarga(
                        $paket,
                        $data['tipe_kamar']
                    ),
                    'diskon'           => $data['diskon'] ?? 0,
                ];
            }

            /* =====================================================
            | UPDATE
            ===================================================== */
            $jamaah->update($payload);

            /* =====================================================
            | AUDIT
            ===================================================== */
            $audit = \App\Services\Jamaah\JamaahAuditService::log(
                $jamaah,
                'UPDATE',
                $old,
                $payload
            );

            // Optional
            // $changes = $audit->changes();

            return $jamaah;
        });
    }

    /* ============================================================
     | HELPER HARGA
     ============================================================ */
    private function resolveHarga($paket, string $tipe): int
    {
        $harga = match ($tipe) {
            'double' => (int) $paket->harga_double,
            'triple' => (int) $paket->harga_triple,
            default  => (int) $paket->harga_quad,
        };

        if ($harga <= 0) {
            throw new Exception('Harga paket belum diset.');
        }

        return $harga;
    }

    /* ============================================================
    | GENERATE AUTO NO ID JAMAAH
    | Format: JMH-2025-00001
    ============================================================ */
    public function generateNoId(): string
    {
        return DB::transaction(function () {

            $year = Carbon::now()->format('Y');

            // 🔒 LOCK TABLE (ANTI DOBEL)
            $last = DB::table('jamaah')
                ->where('no_id', 'like', "JMH-{$year}-%")
                ->lockForUpdate()
                ->orderBy('no_id', 'desc')
                ->value('no_id');

            $nextNumber = 1;

            if ($last) {
                $lastNumber = (int) substr($last, -5);
                $nextNumber = $lastNumber + 1;
            }

            return sprintf(
                'JMH-%s-%05d',
                $year,
                $nextNumber
            );
        });
    }

    public function createFromClosing(array $data): Jamaah
    {
        return DB::transaction(function () use ($data) {

            return Jamaah::create([
                'no_id'             => $this->generateNoId(), // ✅ FIX
                'nama_lengkap'      => $data['nama_lengkap'],
                'nama_ayah'         => '-',
                'nik'               => '-',
                'tempat_lahir'      => '-',
                'tanggal_lahir'     => now()->subYears(30),
                'status_pernikahan' => 'Belum Menikah',
                'jenis_kelamin'     => 'L',
                'paket'             => 'TBD',

                'no_hp'             => $data['no_hp'],
                'branch_id'         => $data['branch_id'] ?? null,
                'agent_id'          => $data['agent_id'] ?? null,

                'status'            => 'pending',
            ]);
        });
    }

}
