<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Marketing\AddonController;
use App\Http\Controllers\Marketing\CampaignController;
use App\Http\Controllers\Marketing\BannerController;
use App\Http\Controllers\Marketing\VoucherController;
use App\Http\Controllers\Marketing\AgentCommissionController;
use App\Http\Controllers\Marketing\FlashSaleController;

Route::prefix('marketing')
    ->middleware(['auth'])
    ->as('marketing.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | ADD-ON
        |--------------------------------------------------------------------------
        */
        Route::resource('addons', AddonController::class)
            ->middleware([
                'index'   => 'permission:addon.view',
                'show'    => 'permission:addon.view',
                'create'  => 'permission:addon.create',
                'store'   => 'permission:addon.create',
                'edit'    => 'permission:addon.update',
                'update'  => 'permission:addon.update',
                'destroy' => 'permission:addon.delete',
            ]);

        /*
        |--------------------------------------------------------------------------
        | CAMPAIGN
        |--------------------------------------------------------------------------
        */
        Route::resource('campaigns', CampaignController::class)
            ->middleware([
                'index'   => 'permission:campaign.view',
                'show'    => 'permission:campaign.view',
                'create'  => 'permission:campaign.create',
                'store'   => 'permission:campaign.create',
                'edit'    => 'permission:campaign.update',
                'update'  => 'permission:campaign.update',
                'destroy' => 'permission:campaign.delete',
            ]);

        Route::post('campaigns/{campaign}/activate',
            [CampaignController::class, 'activate']
        )->name('campaigns.activate')
         ->middleware('permission:campaign.update');

        Route::post('campaigns/{campaign}/finish',
            [CampaignController::class, 'finish']
        )->name('campaigns.finish')
         ->middleware('permission:campaign.update');

        Route::post('campaigns/{campaign}/cancel',
            [CampaignController::class, 'cancel']
        )->name('campaigns.cancel')
         ->middleware('permission:campaign.update');

        /*
        |--------------------------------------------------------------------------
        | BANNERS
        |--------------------------------------------------------------------------
        */
        Route::resource('banners', BannerController::class)
            ->middleware([
                'index'   => 'permission:banner.view',
                'show'    => 'permission:banner.view',
                'create'  => 'permission:banner.create',
                'store'   => 'permission:banner.create',
                'edit'    => 'permission:banner.update',
                'update'  => 'permission:banner.update',
                'destroy' => 'permission:banner.delete',
            ]);

        Route::post('banners/{banner}/publish',
            [BannerController::class, 'publish']
        )->name('banners.publish')
         ->middleware('permission:banner.update');

        Route::post('banners/{banner}/archive',
            [BannerController::class, 'archive']
        )->name('banners.archive')
         ->middleware('permission:banner.update');

        /*
        |--------------------------------------------------------------------------
        | VOUCHERS
        |--------------------------------------------------------------------------
        */
        Route::resource('vouchers', VoucherController::class)
            ->middleware([
                'index'   => 'permission:voucher.view',
                'show'    => 'permission:voucher.view',
                'create'  => 'permission:voucher.create',
                'store'   => 'permission:voucher.create',
                'edit'    => 'permission:voucher.update',
                'update'  => 'permission:voucher.update',
                'destroy' => 'permission:voucher.delete',
            ]);

        /*
        |--------------------------------------------------------------------------
        | FLASH SALES
        |--------------------------------------------------------------------------
        */
        Route::resource('flash-sales', FlashSaleController::class)
            ->middleware([
                'index'   => 'permission:flashsale.view',
                'show'    => 'permission:flashsale.view',
                'create'  => 'permission:flashsale.create',
                'store'   => 'permission:flashsale.create',
                'edit'    => 'permission:flashsale.update',
                'update'  => 'permission:flashsale.update',
                'destroy' => 'permission:flashsale.delete',
            ]);

        /*
        |--------------------------------------------------------------------------
        | AGENT COMMISSION (VIEW ONLY)
        |--------------------------------------------------------------------------
        */
        Route::get('agent-commissions',
            [AgentCommissionController::class, 'index']
        )->name('agent-commissions.index')
         ->middleware('permission:agent.performance.view');

    });