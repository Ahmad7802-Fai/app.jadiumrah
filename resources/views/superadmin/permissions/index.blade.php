@extends('layouts.admin')

@section('content')

<div class="d-flex justify-content-between mb-3">
    <h4>Manajemen Permission</h4>
    <a href="{{ route('superadmin.permissions.create') }}" class="btn btn-primary">Tambah Permission</a>
</div>

<table class="table table-bordered">
    <thead class="bg-light">
        <tr>
            <th>#</th>
            <th>Key</th>
            <th>Nama</th>
            <th width="140">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($permissions as $p)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $p->perm_key }}</td>
            <td>{{ $p->perm_name }}</td>
            <td>
                <a href="{{ route('superadmin.permissions.edit',$p->id) }}" class="btn btn-sm btn-warning">Edit</a>
                <form method="POST" action="{{ route('superadmin.permissions.destroy',$p->id) }}" class="d-inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Hapus permission?')" class="btn btn-sm btn-danger">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
