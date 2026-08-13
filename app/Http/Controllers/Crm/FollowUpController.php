<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    public function index() { return view('sales.followup.index'); }
    public function create() { return view('sales.followup.create'); }
    public function store(Request $req) { /* ... */ }
    public function show($id) { return view('sales.followup.show'); }
    public function edit($id) { return view('sales.followup.edit'); }
    public function update(Request $req, $id) { /* ... */ }
    public function destroy($id) { /* ... */ }
}
