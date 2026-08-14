<?php

use App\Http\Controllers\Api\V1\PublicContentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->group(function () {
        Route::get('/site', [PublicContentController::class, 'site'])
            ->name('site');

        Route::get('/paket-umrah', [PublicContentController::class, 'packages'])
            ->name('packages.index');

        Route::get('/paket-umrah/{slug}', [PublicContentController::class, 'package'])
            ->name('packages.show');

        Route::get('/berita', [PublicContentController::class, 'news'])
            ->name('news.index');

        Route::get('/berita/{slug}', [PublicContentController::class, 'newsDetail'])
            ->name('news.show');

        Route::get('/gallery', [PublicContentController::class, 'gallery'])
            ->name('gallery.index');

        Route::get('/team', [PublicContentController::class, 'team'])
            ->name('team.index');

        Route::get('/testimoni', [PublicContentController::class, 'testimonials'])
            ->name('testimonials.index');
    });
