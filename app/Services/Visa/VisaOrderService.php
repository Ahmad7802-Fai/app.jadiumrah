<?php

namespace App\Services\Visa;

use App\Models\VisaOrder;
use App\Models\VisaOrderTraveler;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class VisaOrderService
{
    public function __construct(
        protected VisaStatusService $statusService,
        protected VisaNoteService $noteService
    ) {
    }

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return VisaOrder::query()
            ->with([
                'product',
                'user',
                'travelers',
                'payments',
            ])
            ->search($filters['keyword'] ?? null)
            ->status($filters['order_status'] ?? null)
            ->paymentStatus($filters['payment_status'] ?? null)
            ->when(!empty($filters['departure_date']), function ($query) use ($filters) {
                $query->whereDate('departure_date', $filters['departure_date']);
            })
            ->latestFirst()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getById(int $id): VisaOrder
    {
        return VisaOrder::query()
            ->with([
                'product',
                'user',
                'travelers.documents',
                'documents',
                'payments',
                'statusHistories.changedBy',
                'notes.user',
            ])
            ->findOrFail($id);
    }

    public function create(array $data): VisaOrder
    {
        return DB::transaction(function () use ($data) {
            $travelers = $data['travelers'] ?? [];
            unset($data['travelers']);

            $order = VisaOrder::query()->create([
                'user_id' => $data['user_id'] ?? null,
                'visa_product_id' => $data['visa_product_id'],
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'],
                'customer_address' => $data['customer_address'] ?? null,
                'total_travelers' => count($travelers) > 0 ? count($travelers) : (int) ($data['total_travelers'] ?? 1),
                'departure_date' => $data['departure_date'] ?? null,
                'return_date' => $data['return_date'] ?? null,
                'departure_city' => $data['departure_city'] ?? null,
                'destination_city' => $data['destination_city'] ?? null,
                'order_status' => $data['order_status'] ?? VisaOrder::STATUS_PENDING,
                'payment_status' => $data['payment_status'] ?? VisaOrder::PAYMENT_UNPAID,
                'subtotal' => $data['subtotal'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'admin_fee' => $data['admin_fee'] ?? 0,
                'total_amount' => $data['total_amount'] ?? 0,
                'amount_paid' => $data['amount_paid'] ?? 0,
                'remaining_amount' => $data['remaining_amount'] ?? 0,
                'customer_note' => $data['customer_note'] ?? null,
                'admin_note' => $data['admin_note'] ?? null,
            ]);

            foreach ($travelers as $index => $travelerData) {
                $order->travelers()->create([
                    'full_name' => $travelerData['full_name'],
                    'gender' => $travelerData['gender'] ?? null,
                    'relationship' => $travelerData['relationship'] ?? null,
                    'is_main_applicant' => $travelerData['is_main_applicant'] ?? ($index === 0),
                    'place_of_birth' => $travelerData['place_of_birth'] ?? null,
                    'date_of_birth' => $travelerData['date_of_birth'] ?? null,
                    'nationality' => $travelerData['nationality'] ?? 'Indonesia',
                    'nik' => $travelerData['nik'] ?? null,
                    'passport_number' => $travelerData['passport_number'] ?? null,
                    'passport_issue_date' => $travelerData['passport_issue_date'] ?? null,
                    'passport_expiry_date' => $travelerData['passport_expiry_date'] ?? null,
                    'passport_issue_place' => $travelerData['passport_issue_place'] ?? null,
                    'address' => $travelerData['address'] ?? null,
                    'phone' => $travelerData['phone'] ?? null,
                    'email' => $travelerData['email'] ?? null,
                    'father_name' => $travelerData['father_name'] ?? null,
                    'mother_name' => $travelerData['mother_name'] ?? null,
                    'notes' => $travelerData['notes'] ?? null,
                ]);
            }

            $this->statusService->updateStatus(
                $order,
                $order->order_status,
                'Order visa dibuat',
                $data['created_by'] ?? $data['user_id'] ?? null
            );

            if (!empty($data['admin_note'])) {
                $this->noteService->create(
                    $order,
                    $data['admin_note'],
                    'internal',
                    $data['created_by'] ?? $data['user_id'] ?? null
                );
            }

            return $order->fresh([
                'product',
                'user',
                'travelers',
                'payments',
                'statusHistories',
                'notes',
            ]);
        });
    }

    public function update(VisaOrder $order, array $data): VisaOrder
    {
        return DB::transaction(function () use ($order, $data) {
            $order->update([
                'user_id' => $data['user_id'] ?? $order->user_id,
                'visa_product_id' => $data['visa_product_id'] ?? $order->visa_product_id,
                'customer_name' => $data['customer_name'] ?? $order->customer_name,
                'customer_email' => $data['customer_email'] ?? $order->customer_email,
                'customer_phone' => $data['customer_phone'] ?? $order->customer_phone,
                'customer_address' => $data['customer_address'] ?? $order->customer_address,
                'total_travelers' => $data['total_travelers'] ?? $order->total_travelers,
                'departure_date' => $data['departure_date'] ?? $order->departure_date,
                'return_date' => $data['return_date'] ?? $order->return_date,
                'departure_city' => $data['departure_city'] ?? $order->departure_city,
                'destination_city' => $data['destination_city'] ?? $order->destination_city,
                'subtotal' => $data['subtotal'] ?? $order->subtotal,
                'discount_amount' => $data['discount_amount'] ?? $order->discount_amount,
                'admin_fee' => $data['admin_fee'] ?? $order->admin_fee,
                'total_amount' => $data['total_amount'] ?? $order->total_amount,
                'customer_note' => $data['customer_note'] ?? $order->customer_note,
                'admin_note' => $data['admin_note'] ?? $order->admin_note,
            ]);

            return $order->fresh([
                'product',
                'user',
                'travelers',
                'documents',
                'payments',
                'statusHistories',
                'notes',
            ]);
        });
    }

    public function delete(VisaOrder $order): bool
    {
        return (bool) $order->delete();
    }

    public function addTraveler(VisaOrder $order, array $data): VisaOrderTraveler
    {
        $traveler = $order->travelers()->create([
            'full_name' => $data['full_name'],
            'gender' => $data['gender'] ?? null,
            'relationship' => $data['relationship'] ?? null,
            'is_main_applicant' => $data['is_main_applicant'] ?? false,
            'place_of_birth' => $data['place_of_birth'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'nationality' => $data['nationality'] ?? 'Indonesia',
            'nik' => $data['nik'] ?? null,
            'passport_number' => $data['passport_number'] ?? null,
            'passport_issue_date' => $data['passport_issue_date'] ?? null,
            'passport_expiry_date' => $data['passport_expiry_date'] ?? null,
            'passport_issue_place' => $data['passport_issue_place'] ?? null,
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'father_name' => $data['father_name'] ?? null,
            'mother_name' => $data['mother_name'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $order->update([
            'total_travelers' => $order->travelers()->count(),
        ]);

        return $traveler;
    }

    public function updateTraveler(VisaOrderTraveler $traveler, array $data): VisaOrderTraveler
    {
        $traveler->update([
            'full_name' => $data['full_name'] ?? $traveler->full_name,
            'gender' => $data['gender'] ?? $traveler->gender,
            'relationship' => $data['relationship'] ?? $traveler->relationship,
            'is_main_applicant' => $data['is_main_applicant'] ?? $traveler->is_main_applicant,
            'place_of_birth' => $data['place_of_birth'] ?? $traveler->place_of_birth,
            'date_of_birth' => $data['date_of_birth'] ?? $traveler->date_of_birth,
            'nationality' => $data['nationality'] ?? $traveler->nationality,
            'nik' => $data['nik'] ?? $traveler->nik,
            'passport_number' => $data['passport_number'] ?? $traveler->passport_number,
            'passport_issue_date' => $data['passport_issue_date'] ?? $traveler->passport_issue_date,
            'passport_expiry_date' => $data['passport_expiry_date'] ?? $traveler->passport_expiry_date,
            'passport_issue_place' => $data['passport_issue_place'] ?? $traveler->passport_issue_place,
            'address' => $data['address'] ?? $traveler->address,
            'phone' => $data['phone'] ?? $traveler->phone,
            'email' => $data['email'] ?? $traveler->email,
            'father_name' => $data['father_name'] ?? $traveler->father_name,
            'mother_name' => $data['mother_name'] ?? $traveler->mother_name,
            'notes' => $data['notes'] ?? $traveler->notes,
        ]);

        return $traveler->fresh();
    }

    public function deleteTraveler(VisaOrderTraveler $traveler): bool
    {
        $order = $traveler->order;
        $deleted = (bool) $traveler->delete();

        if ($order) {
            $order->update([
                'total_travelers' => $order->travelers()->count(),
            ]);
        }

        return $deleted;
    }

    public function updateOrderStatus(
        VisaOrder $order,
        string $status,
        ?string $description = null,
        ?int $changedBy = null
    ): VisaOrder {
        return $this->statusService->updateStatus($order, $status, $description, $changedBy);
    }

    public function addNote(
        VisaOrder $order,
        string $note,
        string $type = 'internal',
        ?int $userId = null
    ) {
        return $this->noteService->create($order, $note, $type, $userId);
    }

    public function getDashboardSummary(): array
    {
        return [
            'total_orders' => VisaOrder::query()->count(),
            'pending_orders' => VisaOrder::query()->where('order_status', VisaOrder::STATUS_PENDING)->count(),
            'processing_orders' => VisaOrder::query()->where('order_status', VisaOrder::STATUS_PROCESSING)->count(),
            'approved_orders' => VisaOrder::query()->where('order_status', VisaOrder::STATUS_APPROVED)->count(),
            'completed_orders' => VisaOrder::query()->where('order_status', VisaOrder::STATUS_COMPLETED)->count(),
            'unpaid_orders' => VisaOrder::query()->where('payment_status', VisaOrder::PAYMENT_UNPAID)->count(),
            'partial_orders' => VisaOrder::query()->where('payment_status', VisaOrder::PAYMENT_PARTIAL)->count(),
            'paid_orders' => VisaOrder::query()->where('payment_status', VisaOrder::PAYMENT_PAID)->count(),
            'total_revenue_paid' => (float) VisaOrder::query()->sum('amount_paid'),
            'total_revenue_expected' => (float) VisaOrder::query()->sum('total_amount'),
        ];
    }

    public function getRecentOrders(int $limit = 10): Collection
    {
        return VisaOrder::query()
            ->with(['product'])
            ->latest('id')
            ->limit($limit)
            ->get();
    }
}