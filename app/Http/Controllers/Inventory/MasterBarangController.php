<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterBarangController extends Controller
{
    public function index() { return view('inventory.master-barang.index'); }
    public function create() { return view('inventory.master-barang.create'); }
    public function store(Request $req) { /* ... */ }
    public function show($id) { return view('inventory.master-barang.show'); }
    public function edit($id) { return view('inventory.master-barang.edit'); }
    public function update(Request $req, $id) { /* ... */ }
    public function destroy($id) { /* ... */ }
}
