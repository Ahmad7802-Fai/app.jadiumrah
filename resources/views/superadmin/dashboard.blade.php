@extends('layouts.admin')
@section('content')
    <div class="card p-4">
        <h4>Dashboard Superadmin</h4>
        <p>Halo {{ Auth::user()->name }}! Ini dashboard SUPERADMIN.</p>
    </div>
@endsection
