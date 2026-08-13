<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Helpers\ApiHelper;

/*
|--------------------------------------------------------------------------
| API PUBLIC TARA TRAVEL (v1)
|--------------------------------------------------------------------------
*/

Route::prefix("v1")->group(function () {

    // ==========================
    // BERITA
    // ==========================
    Route::get('/berita', function () {

        $limit = ApiHelper::limit();
        $kategori = request()->kategori;

        $query = \App\Models\Berita::query();

        // Search
        $query = ApiHelper::search($query, ['judul', 'konten']);

        // Filter kategori
        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        $data = $query->latest()->paginate($limit);

        return [
            'success' => true,
            'page'    => $data->currentPage(),
            'total'   => $data->total(),
            'data'    => $data->items(),
        ];
    });


    // ==========================
    // PAKET UMRAH
    // ==========================
    Route::get('/paket-umrah', function () {

        $limit = ApiHelper::limit();

        $min = request()->min_price;
        $max = request()->max_price;

        $query = \App\Models\PaketUmrah::where('is_active', 1);

        // Search
        $query = ApiHelper::search($query, ['nama_paket', 'slug']);

        // Filter price range
        if ($min) {
            $query->where('harga', '>=', $min);
        }

        if ($max) {
            $query->where('harga', '<=', $max);
        }

        $data = $query->latest()->paginate($limit);

        return [
            'success' => true,
            'page'    => $data->currentPage(),
            'total'   => $data->total(),
            'data'    => $data->items(),
        ];
    });


    // ==========================
    // GALLERY
    // ==========================
    Route::get('/gallery', function () {

        $limit = ApiHelper::limit();

        $query = \App\Models\Gallery::query();

        // Search caption
        $query = ApiHelper::search($query, ['caption']);

        $data = $query->latest()->paginate($limit);

        return [
            'success' => true,
            'page'    => $data->currentPage(),
            'total'   => $data->total(),
            'data'    => $data->items(),
        ];
    });


    // ==========================
    // TEAM
    // ==========================
    Route::get('/team', function () {

        $limit = ApiHelper::limit();

        $query = \App\Models\Team::query();

        // Search by nama or jabatan
        $query = ApiHelper::search($query, ['nama', 'jabatan']);

        $data = $query->paginate($limit);

        return [
            'success' => true,
            'page'    => $data->currentPage(),
            'total'   => $data->total(),
            'data'    => $data->items(),
        ];
    });


    // ==========================
    // TESTIMONI
    // ==========================
    Route::get('/testimoni', function () {

        $limit = ApiHelper::limit();

        $query = \App\Models\Testimoni::query();

        // Search by nama
        $query = ApiHelper::search($query, ['nama', 'pesan']);

        $data = $query->latest()->paginate($limit);

        return [
            'success' => true,
            'page'    => $data->currentPage(),
            'total'   => $data->total(),
            'data'    => $data->items(),
        ];
    });

});

// routes/api.php
Route::get('clients/search', function (Request $request) {

    return \App\Models\Client::where('nama','like','%'.$request->q.'%')
        ->orWhere('telepon','like','%'.$request->q.'%')
        ->limit(10)
        ->get(['id','nama','tipe','telepon']);
});