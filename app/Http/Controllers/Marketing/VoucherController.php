<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::latest();

        if ($request->search) {
            $query->where('code','like','%'.$request->search.'%');
        }

        $vouchers = $query->paginate(15);

        return view('marketing.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('marketing.vouchers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'         => 'required|unique:vouchers,code',
            'type'         => 'required|in:fixed,percent',
            'value'        => 'required|numeric|min:1',
            'max_discount' => 'nullable|numeric|min:0',
            'quota'        => 'nullable|integer|min:1',
            'expired_at'   => 'nullable|date',
        ]);

        Voucher::create($validated);

        return redirect()
            ->route('marketing.vouchers.index')
            ->with('success','Voucher berhasil dibuat.');
    }

    public function edit(Voucher $voucher)
    {
        return view('marketing.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'code'         => 'required|unique:vouchers,code,'.$voucher->id,
            'type'         => 'required|in:fixed,percent',
            'value'        => 'required|numeric|min:1',
            'max_discount' => 'nullable|numeric|min:0',
            'quota'        => 'nullable|integer|min:1',
            'expired_at'   => 'nullable|date',
            'is_active'    => 'boolean'
        ]);

        $voucher->update($validated);

        return redirect()
            ->route('marketing.vouchers.index')
            ->with('success','Voucher diperbarui.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();

        return back()->with('success','Voucher dihapus.');
    }
}