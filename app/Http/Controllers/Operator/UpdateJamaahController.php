<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateJamaahController extends Controller
{
    public function index() { return view('operator.update-jamaah.index'); }
    public function create() { return view('operator.update-jamaah.create'); }
    public function store(Request $request) { /* ... */ }
    public function show($id) { return view('operator.update-jamaah.show'); }
    public function edit($id) { return view('operator.update-jamaah.edit'); }
    public function update(Request $req, $id) { /* ... */ }
    public function destroy($id) { /* ... */ }
}
