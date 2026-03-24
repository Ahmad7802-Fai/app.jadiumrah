<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SavingResource;
use App\Http\Resources\SavingTransactionResource;
use App\Models\SavingAccount;
use App\Services\Saving\SavingService;
use Illuminate\Http\Request;

class SavingController extends Controller
{

    public function __construct(
        protected SavingService $savingService
    ){}

    /*
    |--------------------------------------------------------------------------
    | ACCOUNT INFO
    |--------------------------------------------------------------------------
    */

    public function me(Request $request)
    {

        $account = SavingAccount::with([
            'transactions' => function($q){
                $q->latest()->limit(10);
            },
            'goals'
        ])
        ->where('user_id',$request->user()->id)
        ->first();

        if(!$account){
            return response()->json([
                'data' => null
            ]);
        }

        return new SavingResource($account);

    }


    /*
    |--------------------------------------------------------------------------
    | OPEN ACCOUNT
    |--------------------------------------------------------------------------
    */

    public function openAccount(Request $request)
    {

        $account = $this->savingService->openAccount(
            $request->user()
        );

        return response()->json([
            'message' => 'Rekening tabungan berhasil dibuat',
            'data' => new SavingResource($account)
        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | DEPOSIT
    |--------------------------------------------------------------------------
    */

    public function deposit(Request $request)
    {

        $request->validate([
            'amount' => 'required|numeric|min:10000'
        ]);

        $account = SavingAccount::where(
            'user_id',
            $request->user()->id
        )->firstOrFail();

        $transaction = $this->savingService->deposit(
            $account,
            $request->amount
        );

        return response()->json([
            'message' => 'Setoran berhasil',
            'data' => new SavingTransactionResource($transaction)
        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | WITHDRAW
    |--------------------------------------------------------------------------
    */

    public function withdraw(Request $request)
    {

        $request->validate([
            'amount' => 'required|numeric|min:10000'
        ]);

        $account = SavingAccount::where(
            'user_id',
            $request->user()->id
        )->firstOrFail();

        $transaction = $this->savingService->withdraw(
            $account,
            $request->amount
        );

        return response()->json([
            'message' => 'Penarikan berhasil',
            'data' => new SavingTransactionResource($transaction)
        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | TRANSACTION HISTORY
    |--------------------------------------------------------------------------
    */

    public function transactions(Request $request)
    {

        $account = SavingAccount::where(
            'user_id',
            $request->user()->id
        )->firstOrFail();

        $transactions = $account
            ->transactions()
            ->latest()
            ->paginate(15);

        return SavingTransactionResource::collection($transactions);

    }

}