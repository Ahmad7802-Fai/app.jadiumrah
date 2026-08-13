<?php

namespace App\Services\Saving;

use App\Models\SavingAccount;
use App\Models\SavingTransaction;
use App\Models\Jamaah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SavingService
{

    /*
    |--------------------------------------------------------------------------
    | OPEN ACCOUNT
    |--------------------------------------------------------------------------
    */

    public function openAccount($user)
    {

        return DB::transaction(function() use ($user){

            $jamaah = Jamaah::where('user_id',$user->id)->first();

            if(!$jamaah){
                throw new \Exception("Silakan lengkapi data jamaah terlebih dahulu");
            }

            $accountNumber =
                'TAB-' .
                now()->format('Y') .
                '-' .
                str_pad($jamaah->id,5,'0',STR_PAD_LEFT);

            return SavingAccount::firstOrCreate(

                ['jamaah_id'=>$jamaah->id],

                [
                    'user_id' => $user->id,
                    'account_number' => $accountNumber,
                    'balance' => 0,
                    'status' => 'active'
                ]

            );

        });

    }



    /*
    |--------------------------------------------------------------------------
    | DEPOSIT
    |--------------------------------------------------------------------------
    */

    public function deposit(SavingAccount $account, $amount)
    {

        return DB::transaction(function() use ($account,$amount){

            if($amount <= 0){
                throw new \Exception("Jumlah setoran tidak valid");
            }

            $account = SavingAccount::lockForUpdate()->find($account->id);

            if($account->status !== 'active'){
                throw new \Exception("Rekening tidak aktif");
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE BALANCE
            |--------------------------------------------------------------------------
            */

            $account->increment('balance',$amount);

            $account->refresh();

            /*
            |--------------------------------------------------------------------------
            | GENERATE REFERENCE
            |--------------------------------------------------------------------------
            */

            $reference = $this->generateReference('DEP');

            /*
            |--------------------------------------------------------------------------
            | CREATE TRANSACTION
            |--------------------------------------------------------------------------
            */

            return SavingTransaction::create([

                'saving_account_id' => $account->id,

                'type' => 'deposit',

                'amount' => $amount,

                'reference' => $reference,

                'note' => 'Setor tabungan'

            ]);

        });

    }



    /*
    |--------------------------------------------------------------------------
    | WITHDRAW
    |--------------------------------------------------------------------------
    */

    public function withdraw(SavingAccount $account, $amount)
    {

        return DB::transaction(function() use ($account,$amount){

            if($amount <= 0){
                throw new \Exception("Jumlah penarikan tidak valid");
            }

            $account = SavingAccount::lockForUpdate()->find($account->id);

            if($account->status !== 'active'){
                throw new \Exception("Rekening tidak aktif");
            }

            if($account->balance < $amount){
                throw new \Exception("Saldo tidak cukup");
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE BALANCE
            |--------------------------------------------------------------------------
            */

            $account->decrement('balance',$amount);

            $account->refresh();

            /*
            |--------------------------------------------------------------------------
            | GENERATE REFERENCE
            |--------------------------------------------------------------------------
            */

            $reference = $this->generateReference('WDR');

            /*
            |--------------------------------------------------------------------------
            | CREATE TRANSACTION
            |--------------------------------------------------------------------------
            */

            return SavingTransaction::create([

                'saving_account_id' => $account->id,

                'type' => 'withdraw',

                'amount' => $amount,

                'reference' => $reference,

                'note' => 'Penarikan tabungan'

            ]);

        });

    }



    /*
    |--------------------------------------------------------------------------
    | GET USER ACCOUNT
    |--------------------------------------------------------------------------
    */

    public function getUserAccount($user)
    {

        return SavingAccount::where('user_id',$user->id)
            ->with([
                'transactions' => function($q){
                    $q->latest()->limit(20);
                },
                'goals'
            ])
            ->first();

    }



    /*
    |--------------------------------------------------------------------------
    | GENERATE REFERENCE
    |--------------------------------------------------------------------------
    */

    protected function generateReference($type)
    {

        return $type
            .'-'.
            now()->format('Ymd')
            .'-'.
            strtoupper(Str::random(6));

    }

}