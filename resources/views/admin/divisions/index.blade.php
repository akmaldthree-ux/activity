@extends('layouts.app')

@section('title', 'Kelola Divisi')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-diagram-3 me-1 text-primary"></i>Kelola Divisi</h5>
    <a href="{{ route('admin.divisions.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Tambah Divisi
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th class="d-none d-md-table-cell">Deskripsi</th>
                        <th class="text-center">Anggota</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($divisions as $div)
                    <tr>
                        <td class="fw-semibold">{{ $div->name }}</td>
                        <td class="d-none d-md-table-cell small text-muted">{{ $div->description ?? '—' }}</td>
                        <td class="text-center">{{ $div->users_count }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.divisions.edit', $div) }}" class="btn btn-xs btn-outline-primary py-0 px-2">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.divisions.destroy', $div) }}" method="POST"
                                      onsubmit="return confirm('Hapus divisi {{ $div->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger py-0 px-2">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
