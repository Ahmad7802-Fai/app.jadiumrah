@extends('layouts.app')

@section('title', 'Create Commission Scheme')

@section('content')

<div class="page-container">

    <div class="page-header">
        <h1 class="page-title">Create Commission Scheme</h1>

        <a href="{{ route('commission.schemes.index') }}" class="btn-secondary">
            ← Back
        </a>
    </div>

    <div class="card max-w-2xl">

        <form method="POST" action="{{ route('commission.schemes.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Scheme Name</label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       class="form-input"
                       required>
            </div>

            <div class="form-group">
                <label class="form-label">Year</label>
                <input type="number"
                       name="year"
                       value="{{ old('year', date('Y')) }}"
                       class="form-input"
                       required>
            </div>

            <div class="form-group">
                <label class="form-label flex items-center gap-2">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           class="form-checkbox">
                    Set as Active Scheme
                </label>
            </div>

            <div class="mt-6">
                <button type="submit" class="btn-primary">
                    Save Scheme
                </button>
            </div>

        </form>

    </div>

</div>

@endsection