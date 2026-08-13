@extends('layouts.app')

@section('title', 'Edit Commission Scheme')

@section('content')

<div class="page-container">

    <div class="page-header">
        <h1 class="page-title">Edit Commission Scheme</h1>

        <a href="{{ route('commission.schemes.index') }}" class="btn-secondary">
            ← Back
        </a>
    </div>

    <div class="card max-w-2xl">

        <form method="POST" action="{{ route('commission.schemes.update', $commissionScheme) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Scheme Name</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $commissionScheme->name) }}"
                       class="form-input"
                       required>
            </div>

            <div class="form-group">
                <label class="form-label">Year</label>
                <input type="number"
                       name="year"
                       value="{{ old('year', $commissionScheme->year) }}"
                       class="form-input"
                       required>
            </div>

            <div class="form-group">
                <label class="form-label flex items-center gap-2">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           class="form-checkbox"
                           {{ $commissionScheme->is_active ? 'checked' : '' }}>
                    Active Scheme
                </label>
            </div>

            <div class="mt-6 flex gap-4">

                <button type="submit" class="btn-primary">
                    Update Scheme
                </button>

                @can('commission.delete')
                <form method="POST"
                      action="{{ route('commission.schemes.destroy', $commissionScheme) }}"
                      onsubmit="return confirm('Delete this scheme?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">
                        Delete
                    </button>
                </form>
                @endcan

            </div>

        </form>

    </div>

</div>

@endsection