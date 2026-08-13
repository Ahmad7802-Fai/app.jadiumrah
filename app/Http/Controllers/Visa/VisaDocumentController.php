<?php

namespace App\Http\Controllers\Visa;

use App\Http\Controllers\Controller;
use App\Models\VisaOrder;
use App\Models\VisaOrderDocument;
use App\Models\VisaOrderTraveler;
use App\Services\Visa\VisaDocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VisaDocumentController extends Controller
{
    public function __construct(
        protected VisaDocumentService $visaDocumentService
    ) {
    }

    public function index(Request $request): View
    {
        $documents = VisaOrderDocument::query()
            ->with(['order', 'traveler'])
            ->when($request->filled('visa_order_id'), function ($query) use ($request) {
                $query->where('visa_order_id', $request->integer('visa_order_id'));
            })
            ->when($request->filled('document_type'), function ($query) use ($request) {
                $query->where('document_type', $request->string('document_type')->toString());
            })
            ->latest('id')
            ->paginate((int) $request->input('per_page', 15))
            ->withQueryString();

        return view('visa.documents.index', [
            'pageTitle' => 'Dokumen Visa',
            'documents' => $documents,
        ]);
    }

    public function create(Request $request): View
    {
        $order = null;
        $travelers = collect();

        if ($request->filled('visa_order_id')) {
            $order = VisaOrder::query()
                ->with('travelers')
                ->findOrFail($request->integer('visa_order_id'));

            $travelers = $order->travelers;
        }

        return view('visa.documents.create', [
            'pageTitle' => 'Upload Dokumen Visa',
            'order' => $order,
            'travelers' => $travelers,
            'document' => new VisaOrderDocument(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'visa_order_id' => ['required', 'exists:visa_orders,id'],
            'visa_order_traveler_id' => ['nullable', 'exists:visa_order_travelers,id'],
            'document_type' => ['required', 'string', 'max:50'],
            'document_name' => ['nullable', 'string', 'max:150'],
            'note' => ['nullable', 'string'],
            'file' => ['required', 'file', 'max:5120'],
        ]);

        $order = VisaOrder::query()->findOrFail($validated['visa_order_id']);

        if (!empty($validated['visa_order_traveler_id'])) {
            $traveler = VisaOrderTraveler::query()->findOrFail($validated['visa_order_traveler_id']);

            abort_unless((int) $traveler->visa_order_id === (int) $order->id, 404);

            $this->visaDocumentService->uploadTravelerDocument(
                $order,
                $traveler,
                $validated,
                $request->file('file')
            );
        } else {
            $this->visaDocumentService->uploadOrderDocument(
                $order,
                $validated,
                $request->file('file')
            );
        }

        return redirect()
            ->route('visa.orders.show', $order)
            ->with('success', 'Dokumen berhasil diunggah.');
    }

    public function show(VisaOrderDocument $visaDocument): View
    {
        $visaDocument->load(['order', 'traveler']);

        return view('visa.documents.show', [
            'pageTitle' => 'Detail Dokumen Visa',
            'document' => $visaDocument,
        ]);
    }

    public function edit(VisaOrderDocument $visaDocument): View
    {
        $visaDocument->load(['order', 'traveler']);

        return view('visa.documents.edit', [
            'pageTitle' => 'Edit Dokumen Visa',
            'document' => $visaDocument,
        ]);
    }

    public function update(Request $request, VisaOrderDocument $visaDocument): RedirectResponse
    {
        $validated = $request->validate([
            'document_type' => ['required', 'string', 'max:50'],
            'document_name' => ['nullable', 'string', 'max:150'],
            'note' => ['nullable', 'string'],
        ]);

        $visaDocument->update([
            'document_type' => $validated['document_type'],
            'document_name' => $validated['document_name'] ?? $visaDocument->document_name,
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()
            ->route('visa.documents.show', $visaDocument)
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(VisaOrderDocument $visaDocument): RedirectResponse
    {
        $orderId = $visaDocument->visa_order_id;

        $this->visaDocumentService->delete($visaDocument);

        return redirect()
            ->route('visa.orders.show', $orderId)
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    public function verify(Request $request, VisaOrderDocument $visaDocument): RedirectResponse
    {
        $validated = $request->validate([
            'note' => ['nullable', 'string'],
        ]);

        $this->visaDocumentService->verify(
            $visaDocument,
            auth()->id(),
            $validated['note'] ?? null
        );

        return back()->with('success', 'Dokumen berhasil diverifikasi.');
    }

    public function unverify(Request $request, VisaOrderDocument $visaDocument): RedirectResponse
    {
        $validated = $request->validate([
            'note' => ['nullable', 'string'],
        ]);

        $this->visaDocumentService->unverify(
            $visaDocument,
            $validated['note'] ?? null
        );

        return back()->with('success', 'Verifikasi dokumen dibatalkan.');
    }

    public function download(VisaOrderDocument $visaDocument): StreamedResponse
    {
        $disk = $visaDocument->file_disk ?: 'public';

        abort_unless(
            $visaDocument->file_path && Storage::disk($disk)->exists($visaDocument->file_path),
            404
        );

        return Storage::disk($disk)->download(
            $visaDocument->file_path,
            $visaDocument->file_name ?: basename($visaDocument->file_path)
        );
    }
}