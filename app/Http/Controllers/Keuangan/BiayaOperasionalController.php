<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BiayaOperasionalController extends Controller
{
    public function index() { return view('keuangan.pembayaran.index'); }
    public function create() { return view('keuangan.pembayaran.create'); }
    public function store(Request $req) { /* ... */ }
    public function show($id) { return view('keuangan.pembayaran.show'); }
    public function edit($id) { return view('keuangan.pembayaran.edit'); }
    public function update(Request $req, $id) { /* ... */ }
    public function destroy($id) { /* ... */ }
}
