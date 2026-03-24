<?php

namespace App\Services\Visa;

use App\Models\VisaOrder;
use App\Models\VisaOrderDocument;
use App\Models\VisaOrderTraveler;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VisaDocumentService
{
    public function uploadOrderDocument(
        VisaOrder $order,
        array $data,
        UploadedFile $file
    ): VisaOrderDocument {
        return DB::transaction(function () use ($order, $data, $file) {
            $disk = $data['file_disk'] ?? 'public';
            $folder = 'visa/orders/' . $order->id . '/documents';

            $path = $file->store($folder, $disk);

            return $order->documents()->create([
                'visa_order_traveler_id' => $data['visa_order_traveler_id'] ?? null,
                'document_type' => $data['document_type'],
                'document_name' => $data['document_name'] ?? $file->getClientOriginalName(),
                'file_path' => $path,
                'file_disk' => $disk,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'is_verified' => false,
                'note' => $data['note'] ?? null,
            ]);
        });
    }

    public function uploadTravelerDocument(
        VisaOrder $order,
        VisaOrderTraveler $traveler,
        array $data,
        UploadedFile $file
    ): VisaOrderDocument {
        return DB::transaction(function () use ($order, $traveler, $data, $file) {
            $disk = $data['file_disk'] ?? 'public';
            $folder = 'visa/orders/' . $order->id . '/travelers/' . $traveler->id;

            $path = $file->store($folder, $disk);

            return $order->documents()->create([
                'visa_order_traveler_id' => $traveler->id,
                'document_type' => $data['document_type'],
                'document_name' => $data['document_name'] ?? $file->getClientOriginalName(),
                'file_path' => $path,
                'file_disk' => $disk,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'is_verified' => false,
                'note' => $data['note'] ?? null,
            ]);
        });
    }

    public function verify(VisaOrderDocument $document, ?int $verifiedBy = null, ?string $note = null): VisaOrderDocument
    {
        $document->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $verifiedBy,
            'note' => $note ?? $document->note,
        ]);

        return $document->fresh();
    }

    public function unverify(VisaOrderDocument $document, ?string $note = null): VisaOrderDocument
    {
        $document->update([
            'is_verified' => false,
            'verified_at' => null,
            'verified_by' => null,
            'note' => $note ?? $document->note,
        ]);

        return $document->fresh();
    }

    public function delete(VisaOrderDocument $document): bool
    {
        if ($document->file_path && Storage::disk($document->file_disk ?: 'public')->exists($document->file_path)) {
            Storage::disk($document->file_disk ?: 'public')->delete($document->file_path);
        }

        return (bool) $document->delete();
    }
}