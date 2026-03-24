<?php

namespace App\Http\Controllers\Api\V1\Visa;

use App\Http\Controllers\Controller;
use App\Models\VisaOrderDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VisaDocumentApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = VisaOrderDocument::query()->with(['order', 'traveler']);

        if ($request->filled('visa_order_id')) {
            $query->where('visa_order_id', $request->visa_order_id);
        }

        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        $documents = $query->latest()->paginate($request->integer('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => 'Daftar dokumen visa berhasil diambil.',
            'data' => $documents,
        ]);
    }

    public function show(VisaOrderDocument $visaDocument): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail dokumen visa berhasil diambil.',
            'data' => $visaDocument->load(['order', 'traveler']),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'visa_order_id'          => ['required', 'exists:visa_orders,id'],
            'visa_order_traveler_id' => ['nullable', 'exists:visa_order_travelers,id'],
            'document_type'          => ['required', 'string', 'max:100'],
            'document_file'          => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'notes'                  => ['nullable', 'string'],
        ]);

        $file = $request->file('document_file');
        $path = $file->store('visa/documents', 'public');

        $document = VisaOrderDocument::create([
            'visa_order_id'          => $validated['visa_order_id'],
            'visa_order_traveler_id' => $validated['visa_order_traveler_id'] ?? null,
            'document_type'          => $validated['document_type'],
            'file_name'              => $file->getClientOriginalName(),
            'file_path'              => $path,
            'file_mime'              => $file->getClientMimeType(),
            'file_size'              => $file->getSize(),
            'verification_status'    => 'pending',
            'notes'                  => $validated['notes'] ?? null,
            'uploaded_by'            => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen visa berhasil diunggah.',
            'data' => $document->load(['order', 'traveler']),
        ], 201);
    }

    public function update(Request $request, VisaOrderDocument $visaDocument): JsonResponse
    {
        $validated = $request->validate([
            'document_type' => ['required', 'string', 'max:100'],
            'notes'         => ['nullable', 'string'],
        ]);

        $visaDocument->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen visa berhasil diperbarui.',
            'data' => $visaDocument->fresh()->load(['order', 'traveler']),
        ]);
    }

    public function destroy(VisaOrderDocument $visaDocument): JsonResponse
    {
        if ($visaDocument->file_path && Storage::disk('public')->exists($visaDocument->file_path)) {
            Storage::disk('public')->delete($visaDocument->file_path);
        }

        $visaDocument->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dokumen visa berhasil dihapus.',
        ]);
    }

    public function verify(Request $request, VisaOrderDocument $visaDocument): JsonResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $visaDocument->update([
            'verification_status' => 'verified',
            'verified_at'         => now(),
            'verified_by'         => auth()->id(),
            'notes'               => $validated['notes'] ?? $visaDocument->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen visa berhasil diverifikasi.',
            'data' => $visaDocument->fresh(),
        ]);
    }

    public function unverify(Request $request, VisaOrderDocument $visaDocument): JsonResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $visaDocument->update([
            'verification_status' => 'rejected',
            'verified_at'         => null,
            'verified_by'         => auth()->id(),
            'notes'               => $validated['notes'] ?? $visaDocument->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen visa berhasil ditandai tidak valid.',
            'data' => $visaDocument->fresh(),
        ]);
    }

    public function download(VisaOrderDocument $visaDocument): StreamedResponse
    {
        return Storage::disk('public')->download(
            $visaDocument->file_path,
            $visaDocument->file_name
        );
    }
}